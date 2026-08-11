<?php

namespace App\Events;

use App\Models\AssignmentSubmission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast saat siswa mengumpulkan tugas.
 * Channel: assignments.{assignment_id}
 * Didengar oleh panel guru (admin.blade.php).
 */
class AssignmentSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public AssignmentSubmission $submission) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('assignments.' . $this->submission->assignment_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'submission.created';
    }

    public function broadcastWith(): array
    {
        return [
            'submission_id' => $this->submission->id,
            'assignment_id' => $this->submission->assignment_id,
            'student_name'  => $this->submission->student_name,
            'student_class' => $this->submission->student_class,
            'file_name'     => $this->submission->file_name,
            'file_ext'      => $this->submission->file_ext,
            'file_size'     => $this->submission->file_size,
            'submitted_at'  => $this->submission->submitted_at->format('d M, H:i'),
            'status'        => $this->submission->status,
        ];
    }
}
