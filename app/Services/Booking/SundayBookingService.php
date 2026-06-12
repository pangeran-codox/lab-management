<?php

namespace App\Services\Booking;

use App\Events\ScheduleUpdated;
use App\Models\SundayBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SundayBookingService
{
    public function approve(SundayBooking $booking, Request $request): SundayBooking
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

        // Broadcast perubahan via Reverb
        broadcast(new ScheduleUpdated('sunday', 'updated', [
            'resource_id'  => $booking->resource_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'status'       => 'approved'
        ]));

        return $booking;
    }

    public function reject(SundayBooking $booking, string $notes): void
    {
        $booking->update([
            'status' => 'rejected',
            'notes'  => $notes,
        ]);

        // Broadcast perubahan via Reverb
        broadcast(new ScheduleUpdated('sunday', 'updated', [
            'resource_id'  => $booking->resource_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'status'       => 'rejected'
        ]));
    }

    public function destroy(SundayBooking $booking): string
    {
        $title = $booking->title;
        $data = [
            'resource_id'  => $booking->resource_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'status'       => 'deleted'
        ];

        $booking->delete();

        // Broadcast perubahan via Reverb
        broadcast(new ScheduleUpdated('sunday', 'deleted', $data));

        return $title;
    }
}