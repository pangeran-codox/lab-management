<?php

namespace App\Services\Booking;

use App\Models\SundayBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SundayBookingService
{
    public function approve(SundayBooking $booking, Request $request): void
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
    }

    public function reject(SundayBooking $booking, string $notes): void
    {
        $booking->update([
            'status' => 'rejected',
            'notes'  => $notes,
        ]);
    }

    public function destroy(SundayBooking $booking): string
    {
        $title = $booking->title;
        $booking->delete();
        return $title;
    }
}