<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $booking)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('bookings'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'           => $this->booking->id,
            'resource_id'  => $this->booking->resource_id,
            'title'        => $this->booking->title,
            'teacher_name' => $this->booking->teacher_name,
            'resource'     => $this->booking->resource->name ?? '-',
            'booking_date' => $this->booking->booking_date instanceof \Carbon\Carbon 
                                ? $this->booking->booking_date->toDateString() 
                                : $this->booking->booking_date,
            'status'       => $this->booking->status,
        ];
    }
}