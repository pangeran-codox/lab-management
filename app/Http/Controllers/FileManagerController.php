<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\LabJournalPhoto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all'); // all | jurnal | tugas

        // ─── FOTO JURNAL (disk: public, path: lab_journals/...) ───────
        $journalPhotos = LabJournalPhoto::with([
            'journal:id,teacher_name,class_name,journal_date,resource_id',
            'journal.resource:id,name',
        ])
        ->orderByDesc('id')
        ->get()
        ->map(function ($photo) {
            $fullPath  = Storage::disk('public')->path($photo->photo_path);
            $fileSize  = file_exists($fullPath) ? filesize($fullPath) : 0;
            $exists    = $fileSize > 0;

            return [
                'id'            => $photo->id,
                'type'          => 'jurnal',
                'subtype'       => 'jurnal_photo',
                'path'          => $photo->photo_path,
                'url'           => $photo->url,
                'size_bytes'    => $fileSize,
                'size_label'    => $this->formatBytes($fileSize),
                'exists'        => $exists,
                'teacher_name'  => $photo->journal?->teacher_name ?? '-',
                'class_name'    => $photo->journal?->class_name ?? '-',
                'resource_name' => $photo->journal?->resource?->name ?? '-',
                'date'          => $photo->journal?->journal_date
                                    ? Carbon::parse($photo->journal->journal_date)->translatedFormat('d M Y')
                                    : '-',
                'date_raw'      => $photo->journal?->journal_date,
                'ext'           => strtolower(pathinfo($photo->photo_path, PATHINFO_EXTENSION)),
                'created_at'    => null,
                'delete_route'  => 'file-manager.journal.destroy',
                'delete_id'     => $photo->id,
            ];
        });

        // ─── FILE TUGAS SISWA (disk: local, path: submissions/...) ────
        $submissions = AssignmentSubmission::with([
            'assignment:id,title,subject_name,class_name',
        ])
        ->orderByDesc('submitted_at')
        ->get()
        ->map(function ($sub) {
            $fullPath = Storage::disk('local')->path($sub->file_path);
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
            $exists   = $fileSize > 0;

            return [
                'id'             => $sub->id,
                'type'           => 'tugas',
                'subtype'        => 'submission',
                'path'           => $sub->file_path,
                'file_name'      => $sub->file_name,
                'size_bytes'     => $fileSize,
                'size_label'     => $exists ? $this->formatBytes($fileSize) : ($sub->file_size ?? '-'),
                'exists'         => $exists,
                'student_name'   => $sub->student_name,
                'class_name'     => $sub->student_class,
                'assignment'     => $sub->assignment?->title ?? '-',
                'subject_name'   => $sub->assignment?->subject_name ?? '-',
                'date'           => $sub->submitted_at
                                    ? $sub->submitted_at->translatedFormat('d M Y, H:i')
                                    : '-',
                'date_raw'       => $sub->submitted_at,
                'ext'            => strtolower($sub->file_ext ?? pathinfo($sub->file_path, PATHINFO_EXTENSION)),
                'created_at'     => $sub->submitted_at,
                'status'         => $sub->status,
                'grade'          => $sub->grade,
                'download_route' => 'assignment.download',
                'download_id'    => $sub->id,
                'delete_route'   => 'file-manager.submission.destroy',
                'delete_id'      => $sub->id,
            ];
        });

        // ─── LAMPIRAN GURU (disk: local, path lampiran assignment) ────
        $attachments = Assignment::whereNotNull('attachment_path')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($a) {
                $fullPath = Storage::disk('local')->path($a->attachment_path);
                $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                $exists   = $fileSize > 0;

                return [
                    'id'             => $a->id,
                    'type'           => 'tugas',
                    'subtype'        => 'attachment',
                    'path'           => $a->attachment_path,
                    'file_name'      => $a->attachment_name,
                    'size_bytes'     => $fileSize,
                    'size_label'     => $exists ? $this->formatBytes($fileSize) : ($a->attachment_size ?? '-'),
                    'exists'         => $exists,
                    'teacher_name'   => null,
                    'class_name'     => $a->class_name,
                    'assignment'     => $a->title,
                    'subject_name'   => $a->subject_name,
                    'date'           => $a->created_at?->translatedFormat('d M Y, H:i') ?? '-',
                    'date_raw'       => $a->created_at,
                    'ext'            => strtolower(pathinfo($a->attachment_path, PATHINFO_EXTENSION)),
                    'created_at'     => $a->created_at,
                    'download_route' => 'assignment.download.attachment',
                    'download_id'    => $a->id,
                    'delete_route'   => 'file-manager.attachment.destroy',
                    'delete_id'      => $a->id,
                ];
            });

        // ─── STORAGE SUMMARY ──────────────────────────────────────────
        $journalTotalBytes      = $journalPhotos->sum('size_bytes');
        $journalMissingCount    = $journalPhotos->where('exists', false)->count();

        $tugasAll               = $submissions->concat($attachments);
        $tugasTotalBytes        = $tugasAll->sum('size_bytes');
        $tugasMissingCount      = $tugasAll->where('exists', false)->count();

        $grandTotalBytes        = $journalTotalBytes + $tugasTotalBytes;

        $summary = [
            'jurnal' => [
                'count'       => $journalPhotos->count(),
                'total_bytes' => $journalTotalBytes,
                'total_label' => $this->formatBytes($journalTotalBytes),
                'missing'     => $journalMissingCount,
            ],
            'tugas' => [
                'count'       => $tugasAll->count(),
                'total_bytes' => $tugasTotalBytes,
                'total_label' => $this->formatBytes($tugasTotalBytes),
                'missing'     => $tugasMissingCount,
            ],
            'total' => [
                'count'       => $journalPhotos->count() + $tugasAll->count(),
                'total_bytes' => $grandTotalBytes,
                'total_label' => $this->formatBytes($grandTotalBytes),
                'missing'     => $journalMissingCount + $tugasMissingCount,
            ],
        ];

        // ─── FILTER BY TAB ────────────────────────────────────────────
        $files = match ($tab) {
            'jurnal' => $journalPhotos->values(),
            'tugas'  => $tugasAll->sortByDesc('date_raw')->values(),
            default  => $journalPhotos->concat($tugasAll)
                            ->sortByDesc('date_raw')
                            ->values(),
        };

        // ─── FILTER PENCARIAN ─────────────────────────────────────────
        $search = $request->get('search');
        if ($search) {
            $searchLower = strtolower($search);
            $files = $files->filter(function ($f) use ($searchLower) {
                return str_contains(strtolower($f['teacher_name'] ?? ''), $searchLower)
                    || str_contains(strtolower($f['student_name'] ?? ''), $searchLower)
                    || str_contains(strtolower($f['class_name'] ?? ''), $searchLower)
                    || str_contains(strtolower($f['assignment'] ?? ''), $searchLower)
                    || str_contains(strtolower($f['file_name'] ?? ''), $searchLower);
            })->values();
        }

        return view('admin.file-manager', compact('files', 'summary', 'tab', 'search'));
    }

    // ══════════════════════════════════════════════════════════════════
    // DOWNLOAD & DELETE — FOTO JURNAL
    // ══════════════════════════════════════════════════════════════════

    public function downloadJournalPhoto(LabJournalPhoto $photo)
    {
        if (!Storage::disk('public')->exists($photo->photo_path)) {
            return back()->with('error', 'File tidak ditemukan di storage.');
        }

        return Storage::disk('public')->download($photo->photo_path, basename($photo->photo_path));
    }

    public function destroyJournalPhoto(LabJournalPhoto $photo)
    {
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Foto jurnal berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE — SUBMISSION TUGAS SISWA (file + record ikut terhapus)
    // ══════════════════════════════════════════════════════════════════

    public function destroySubmission(AssignmentSubmission $submission)
    {
        Storage::disk('local')->delete($submission->file_path);
        $submission->delete();

        return back()->with('success', 'File submission tugas berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE — LAMPIRAN GURU (cuma file & field attachment, assignment tetap ada)
    // ══════════════════════════════════════════════════════════════════

    public function destroyAttachment(Assignment $assignment)
    {
        if ($assignment->attachment_path) {
            Storage::disk('local')->delete($assignment->attachment_path);
        }

        $assignment->update([
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_size' => null,
        ]);

        return back()->with('success', 'Lampiran tugas berhasil dihapus (data tugas tetap ada).');
    }

    // ══════════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
}