<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeDuplicateTeachers extends Command
{
    protected $signature = 'teachers:merge-duplicates';
    protected $description = 'Merge duplicate teachers berdasarkan nomor HP yang sama';

    public function handle()
    {
        $duplicates = DB::table('teachers')
            ->select('phone', DB::raw('COUNT(*) as total'), DB::raw('MIN(id) as keep_id'))
            ->groupBy('phone')
            ->having('total', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('Tidak ada duplikat ditemukan.');
            return;
        }

        foreach ($duplicates as $dup) {
            $duplicateIds = DB::table('teachers')
                ->where('phone', $dup->phone)
                ->where('id', '!=', $dup->keep_id)
                ->pluck('id');

            $keepTeacher = DB::table('teachers')->find($dup->keep_id);

            $this->warn("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->warn("Phone duplikat: {$dup->phone}");
            $this->line("  → Pertahankan : id={$dup->keep_id} ({$keepTeacher->name})");
            $this->line("  → Hapus id    : " . $duplicateIds->implode(', '));

            // Tampilkan detail yang akan dihapus
            DB::table('teachers')
                ->whereIn('id', $duplicateIds)
                ->get()
                ->each(function ($t) {
                    $this->line("    - id={$t->id} {$t->name} (token: {$t->token})");
                });

            if (!$this->confirm('Lanjutkan merge ini?')) {
                $this->warn('Dilewati.');
                continue;
            }

            DB::transaction(function () use ($dup, $duplicateIds) {
                DB::table('bookings')
                    ->whereIn('teacher_id', $duplicateIds)
                    ->update(['teacher_id' => $dup->keep_id]);

                DB::table('schedules')
                    ->whereIn('teacher_id', $duplicateIds)
                    ->update(['teacher_id' => $dup->keep_id]);

                DB::table('sunday_bookings')
                    ->whereIn('teacher_id', $duplicateIds)
                    ->update(['teacher_id' => $dup->keep_id]);

                DB::table('assignments')
                    ->whereIn('teacher_id', $duplicateIds)
                    ->update(['teacher_id' => $dup->keep_id]);

                DB::table('teachers')
                    ->whereIn('id', $duplicateIds)
                    ->delete();
            });

            $this->info("  ✓ Merge selesai.");
        }

        $this->info('');
        $this->info('Semua duplikat selesai diproses.');
    }
}