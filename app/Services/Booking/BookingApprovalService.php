<?php

namespace App\Services\Booking;

use App\Events\ScheduleUpdated;
use App\Models\Booking;
use App\Services\LabControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingApprovalService
{
    public function __construct(
        private BookingAccessService  $access,
        private LabControlService     $labControl,
        private ConflictCheckerService $conflict, // ← TAMBAH
    ) {}

    // ══════════════════════════════════════════════════════════════════
    // APPROVE SINGLE
    // ══════════════════════════════════════════════════════════════════

    /**
     * @throws \RuntimeException jika ada konflik jadwal
     */
    public function approve(Booking $booking, Request $request): void
    {
        // ─── Cek bentrok sebelum approve ──────────────────────────
        $conflicts = $this->conflict->check($booking);

        if (!empty($conflicts)) {
            throw new \RuntimeException(
                'Tidak dapat menyetujui booking karena ada bentrok: ' .
                implode(' | ', $conflicts)
            );
        }

        DB::transaction(function () use ($booking, $request) {
            $booking->lockForUpdate();
            $booking->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $request->notes,
            ]);
        });

        // Broadcast perubahan via Reverb (Update UI Jadwal)
        broadcast(new ScheduleUpdated('regular', 'updated', [
            'resource_id'  => $booking->resource_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'time_slot_id' => $booking->time_slot_id,
            'status'       => 'approved'
        ]));

        $this->generateSessionAndNotify($booking);
    }

    public function destroy(Booking $booking): string
    {
        $title = $booking->title;
        $data = [
            'resource_id'  => $booking->resource_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'time_slot_id' => $booking->time_slot_id,
            'status'       => 'deleted'
        ];

        $booking->delete();

        // Broadcast perubahan via Reverb agar slot di jadwal langsung kosong
        broadcast(new ScheduleUpdated('regular', 'deleted', $data));

        return $title;
    }

    // ══════════════════════════════════════════════════════════════════
    // APPROVE GROUP
    // ══════════════════════════════════════════════════════════════════

    /**
     * @throws \RuntimeException jika ada konflik di salah satu slot
     */
    public function approveGroup(Request $request): int
    {
        $allowed = $this->access->getAllowedResources();

        $bookings = Booking::where('teacher_name', $request->teacher_name)
            ->where('resource_id', $request->resource_id)
            ->where('booking_date', $request->booking_date)
            ->where('status', 'pending')
            ->when($allowed !== null, fn($q) => $q->whereIn('resource_id', $allowed))
            ->orderBy('time_slot_id')
            ->get();

        if ($bookings->isEmpty()) {
            throw new \RuntimeException('Tidak ada booking pending yang bisa disetujui.');
        }

        // ─── Cek bentrok untuk semua slot dalam group ─────────────
        $groupConflicts = $this->conflict->checkGroup($bookings);

        if (!empty($groupConflicts)) {
            $messages = [];
            foreach ($groupConflicts as $bookingId => $conflicts) {
                $b = $bookings->firstWhere('id', $bookingId);
                $slotName = $b?->timeSlot?->name ?? 'Slot #' . $bookingId;
                $messages[] = $slotName . ': ' . implode(', ', $conflicts);
            }
            throw new \RuntimeException(
                'Beberapa slot ada bentrok: ' . implode(' | ', $messages)
            );
        }

        DB::transaction(function () use ($bookings, $request) {
            Booking::whereIn('id', $bookings->pluck('id'))
                ->lockForUpdate()
                ->update([
                    'status'      => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'notes'       => $request->notes,
                ]);
        });

        // Generate session di luar transaction
        $session = null;
        foreach ($bookings as $booking) {
            try {
                $session = $this->labControl->generateFromBooking($booking->fresh());
            } catch (\Exception $e) {
                Log::warning('generateFromBooking failed for booking #' . $booking->id . ': ' . $e->getMessage());
            }
        }

        if ($session) {
            try {
                $this->labControl->sendWebhook($session->fresh());
            } catch (\Exception $e) {
                Log::warning('sendWebhook failed: ' . $e->getMessage());
            }
        }

        return $bookings->count();
    }

    // ══════════════════════════════════════════════════════════════════
    // REJECT
    // ══════════════════════════════════════════════════════════════════

    public function reject(Booking $booking, string $notes): void
    {
        $booking->update([
            'status' => 'rejected',
            'notes'  => $notes,
        ]);

        // Broadcast perubahan via Reverb (agar slot yang tadi dipesan jadi kosong lagi)
        broadcast(new ScheduleUpdated('regular', 'updated', [
            'resource_id'  => $booking->resource_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'time_slot_id' => $booking->time_slot_id,
            'status'       => 'rejected'
        ]));
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE
    // ══════════════════════════════════════════════════════════════════

    private function generateSessionAndNotify(Booking $booking): void
    {
        try {
            $session = $this->labControl->generateFromBooking($booking->fresh());
            if ($session) {
                $this->labControl->sendWebhook($session->fresh());
            }
        } catch (\Exception $e) {
            Log::warning('Session generation failed for booking #' . $booking->id . ': ' . $e->getMessage());
        }
    }
}