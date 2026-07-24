<?php

namespace App\Http\Controllers;

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
                'id'           => $photo->id,
                'type'         => 'jurnal',
                'path'         => $photo->photo_path,
                'url'          => $photo->url,
                'size_bytes'   => $fileSize,
                'size_label'   => $this->formatBytes($fileSize),
                'exists'       => $exists,
                'teacher_name' => $photo->journal?->teacher_name ?? '-',
                'class_name'   => $photo->journal?->class_name ?? '-',
                'resource_name'=> $photo->journal?->resource?->name ?? '-',
                'date'         => $photo->journal?->journal_date
                                    ? Carbon::parse($photo->journal->journal_date)->translatedFormat('d M Y')
                                    : '-',
                'date_raw'     => $photo->journal?->journal_date,
                'ext'          => strtolower(pathinfo($photo->photo_path, PATHINFO_EXTENSION)),
                'created_at'   => null, // LabJournalPhoto tidak punya timestamps
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
                'id'            => $sub->id,
                'type'          => 'tugas',
                'path'          => $sub->file_path,
                'file_name'     => $sub->file_name,
                'size_bytes'    => $fileSize,
                'size_label'    => $exists ? $this->formatBytes($fileSize) : ($sub->file_size ?? '-'),
                'exists'        => $exists,
                'student_name'  => $sub->student_name,
                'class_name'    => $sub->student_class,
                'assignment'    => $sub->assignment?->title ?? '-',
                'subject_name'  => $sub->assignment?->subject_name ?? '-',
                'date'          => $sub->submitted_at
                                    ? $sub->submitted_at->translatedFormat('d M Y, H:i')
                                    : '-',
                'date_raw'      => $sub->submitted_at,
                'ext'           => strtolower($sub->file_ext ?? pathinfo($sub->file_path, PATHINFO_EXTENSION)),
                'created_at'    => $sub->submitted_at,
                'status'        => $sub->status,
                'grade'         => $sub->grade,
                'download_route'=> 'assignment.download',
                'download_id'   => $sub->id,
            ];
        });

        // ─── STORAGE SUMMARY ──────────────────────────────────────────
        $journalTotalBytes     = $journalPhotos->sum('size_bytes');
        $journalMissingCount   = $journalPhotos->where('exists', false)->count();
        $submissionTotalBytes  = $submissions->sum('size_bytes');
        $submissionMissingCount= $submissions->where('exists', false)->count();
        $grandTotalBytes       = $journalTotalBytes + $submissionTotalBytes;

        $summary = [
            'jurnal' => [
                'count'        => $journalPhotos->count(),
                'total_bytes'  => $journalTotalBytes,
                'total_label'  => $this->formatBytes($journalTotalBytes),
                'missing'      => $journalMissingCount,
            ],
            'tugas'  => [
                'count'        => $submissions->count(),
                'total_bytes'  => $submissionTotalBytes,
                'total_label'  => $this->formatBytes($submissionTotalBytes),
                'missing'      => $submissionMissingCount,
            ],
            'total'  => [
                'count'        => $journalPhotos->count() + $submissions->count(),
                'total_bytes'  => $grandTotalBytes,
                'total_label'  => $this->formatBytes($grandTotalBytes),
                'missing'      => $journalMissingCount + $submissionMissingCount,
            ],
        ];

        // ─── FILTER BY TAB ────────────────────────────────────────────
        $files = match ($tab) {
            'jurnal' => $journalPhotos->values(),
            'tugas'  => $submissions->values(),
            default  => $journalPhotos->concat($submissions)
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
    // DOWNLOAD FILE JURNAL (disk: public, hanya untuk admin)
    // ══════════════════════════════════════════════════════════════════

    public function downloadJournalPhoto(LabJournalPhoto $photo)
    {
        $path = $photo->photo_path;

        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File tidak ditemukan di storage.');
        }

        $filename = basename($path);
        return Storage::disk('public')->download($path, $filename);
    }

    // ══════════════════════════════════════════════════════════════════
    // DELETE FILE JURNAL
    // ══════════════════════════════════════════════════════════════════

    public function destroyJournalPhoto(LabJournalPhoto $photo)
    {
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Foto jurnal berhasil dihapus.');
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
