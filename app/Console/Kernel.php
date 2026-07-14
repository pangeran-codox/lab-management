<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Generate token lab control H-5 menit sebelum jadwal rutin
        $schedule->call(function () {
            // Cek toggle auto-generate dari Settings
            if (!\App\Models\Setting::isEnabled(\App\Models\Setting::LAB_SESSION_AUTO)) {
                return;
            }

            $today  = now()->format('l');
            $target = now()->addMinutes(5);
            $todate = now()->toDateString();

            // Cari slot yang akan mulai dalam 5 menit
            $schedules = \App\Models\Schedule::where('status', 'active')
                ->whereNull('deleted_at')
                ->where('day_of_week', $today)
                ->with('timeSlot')
                ->get()
                ->filter(function ($sch) use ($target, $todate) {
                    if (!$sch->timeSlot) return false;
                    $schedStart = \Carbon\Carbon::parse($todate . ' ' . $sch->timeSlot->start_time);
                    return abs($schedStart->diffInMinutes($target)) <= 1;
                });

            if ($schedules->isEmpty()) return;

            foreach ($schedules as $sch) {
                if (!$sch->timeSlot) continue;

                $sch->start_time = $sch->timeSlot->start_time;
                $sch->end_time   = $sch->timeSlot->end_time;

                // Cek apakah sudah ada session aktif hari ini untuk guru+lab ini
                $existingSession = \App\Models\LabSession::where('source_type', 'schedule')
                    ->where('resource_id', $sch->resource_id)
                    ->whereDate('session_start', $todate)
                    ->where('teacher_name', $sch->teacher_name)
                    ->where('is_active', true)
                    ->first();

                if ($existingSession) {
                    // Session sudah ada, skip (sudah di-lookahead sebelumnya)
                    continue;
                }

                // Cek apakah slot ini sudah pernah dibuat
                $existsBySource = \App\Models\LabSession::where('source_type', 'schedule')
                    ->where('source_id', $sch->id)
                    ->whereDate('session_start', $todate)
                    ->exists();
                if ($existsBySource) continue;

                // Lookahead: cari semua slot berurutan dari guru+lab yang sama hari ini
                $allSlots = \App\Models\Schedule::where('status', 'active')
                    ->whereNull('deleted_at')
                    ->where('day_of_week', $today)
                    ->where('resource_id', $sch->resource_id)
                    ->where('teacher_name', $sch->teacher_name)
                    ->with('timeSlot')
                    ->get()
                    ->filter(fn($s) => $s->timeSlot !== null)
                    ->sortBy(fn($s) => $s->timeSlot->slot_order);

                // Cari slot berurutan mulai dari slot ini (slot_order berurutan tanpa jeda non-istirahat)
                $currentOrder = $sch->timeSlot->slot_order;
                $lastEndTime  = $sch->timeSlot->end_time;

                foreach ($allSlots as $nextSlot) {
                    $order = $nextSlot->timeSlot->slot_order;
                    if ($order <= $currentOrder) continue;

                    // Cek apakah slot berikutnya berurutan (order selisih 1, atau melewati istirahat)
                    $prevEnd  = \Carbon\Carbon::parse($todate . ' ' . $lastEndTime);
                    $nextStart = \Carbon\Carbon::parse($todate . ' ' . $nextSlot->timeSlot->start_time);

                    // Berurutan jika jeda <= 5 menit (toleransi istirahat pendek)
                    if ($nextStart->diffInMinutes($prevEnd) <= 5) {
                        $lastEndTime  = $nextSlot->timeSlot->end_time;
                        $currentOrder = $order;
                    } else {
                        break;
                    }
                }

                // Buat session dengan session_end = slot terakhir yang berurutan
                $labControl = app(\App\Services\LabControlService::class);
                $session = $labControl->generateFromSchedule($sch);

                if ($session && $lastEndTime !== $sch->timeSlot->end_time) {
                    $finalEnd = \Carbon\Carbon::parse($todate . ' ' . $lastEndTime);
                    if ($finalEnd > $session->session_end) {
                        $session->update(['session_end' => $finalEnd]);
                    }
                }

                // Kirim WA sekali dengan jam akhir yang sudah benar
                if ($session) {
                    $labControl->sendWebhook($session->fresh());
                }
            }
        })->everyMinute()->name('lab-session-generator')->withoutOverlapping();

        // Auto-invalidate sesi yang sudah expired
        $schedule->call(function () {
            \App\Models\LabSession::where('is_active', true)
                ->where('session_end', '<', now())
                ->whereNull('invalidated_at')
                ->update([
                    'is_active'          => false,
                    'invalidated_at'     => now(),
                    'invalidated_reason' => 'expired',
                ]);
        })->everyFiveMinutes()->name('lab-session-cleanup')->withoutOverlapping();

        // Prune data Telescope yang lebih dari beberapa jam (untuk menghindari penumpukan)
        $hours = env('TELESCOPE_PRUNE_HOURS', 24);
        $schedule->command("telescope:prune --hours={$hours}")->daily()->name('telescope-prune')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}