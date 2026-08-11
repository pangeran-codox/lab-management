<?php

namespace App\Events;

use App\Models\Assignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast saat guru update tugas (edit, nilai, toggle akses, reopen, hapus submission).
 * Channel: assignments.{assignment_id}
 * Didengar oleh halaman publik siswa (show.blade.php) dan panel guru.
 */
class AssignmentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Assignment $assignment,
        public string $action, // 'graded' | 'updated' | 'submission_deleted' | 'access_changed'
        public array $data = []
    ) {}

    public function broadcastOn(): array
    {
        // Broadcast ke channel per-tugas (untuk panel guru)
        // DAN channel per-kelas (untuk halaman publik siswa)
        $classSlug = \Illuminate\Support\Str::slug($this->assignment->class_name, '_');

        return [
            new Channel('assignments.' . $this->assignment->id),
            new Channel('class_assignments.' . $classSlug),
        ];
    }

    public function broadcastAs(): string
    {
        return 'assignment.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'assignment_id'          => $this->assignment->id,
            'action'                 => $this->action,
            'is_active'              => $this->assignment->is_active,
            'allow_student_download' => $this->assignment->allow_student_download,
            'deadline'               => $this->assignment->deadline->toISOString(),
            'class_name'             => $this->assignment->class_name,
            'title'                  => $this->assignment->title,
            'subject_name'           => $this->assignment->subject_name,
            'teacher_name'           => $this->assignment->teacher->name ?? '',
            'has_attachment'         => !is_null($this->assignment->attachment_path),
            'series_id'              => $this->assignment->series_id,
            'session_number'         => $this->assignment->session_number,
            'data'                   => $this->data,
        ];
    }
}
