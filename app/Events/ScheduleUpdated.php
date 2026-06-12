<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $type 'regular' | 'sunday'
     * @param string $action 'created' | 'updated' | 'deleted'
     * @param array $data Data minimal untuk update UI (resource_id, date, dll)
     */
    public function __construct(
        public string $type,
        public string $action,
        public array $data
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('schedules'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'schedule.updated';
    }
}
