<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Services\LabControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingApprovalService
{
    public function __construct(
        private BookingAccessService $access,
        private LabControlService    $labControl
    ) {}

    public function approve(Booking $booking, Request $request): void
    {
        DB::transaction(function () use ($booking, $request) {
            $booking->lockForUpdate();
            $booking->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $request->notes,
            ]);
        });

        $this->generateSessionAndNotify($booking);
    }

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

    public function reject(Booking $booking, string $notes): void
    {
        $booking->update([
            'status' => 'rejected',
            'notes'  => $notes,
        ]);
    }

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