<?php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentAdminController extends Controller
{
    /**
     * Resolve siapa yang sedang mengakses:
     * - Admin/Teknisi → via auth() login biasa
     * - Guru → via token
     */
    private function resolveAccess(Request $request): array
    {
        // Jika login biasa (admin/teknisi/staff)
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'teknisi', 'staff', 'technician'])) {
            return ['role' => auth()->user()->role, 'teacher' => null];
        }

        // Jika guru pakai token
        $token = $request->query('token') ?? session('teacher_token');
        if ($token) {
            $teacher = Teacher::where('token', strtoupper($token))
                ->where('is_active', true)
                ->first();

            if ($teacher) {
                session(['teacher_token' => $token]);
                return ['role' => 'teacher', 'teacher' => $teacher];
            }
        }

        return ['role' => null, 'teacher' => null];
    }

    public function index(Request $request)
    {
        $access  = $this->resolveAccess($request);
        $role    = $access['role'];
        $teacher = $access['teacher'];

        // Tidak punya akses sama sekali
        if (!$role) {
            return view('assignments.verify-token');
        }

        if ($role === 'teacher') {
            // Guru hanya lihat tugasnya sendiri
            $assignments = Assignment::with('submissions')
                ->where('teacher_id', $teacher->id)
                ->orderByDesc('created_at')
                ->get();
        } else {
            // Admin/Teknisi lihat semua tugas
            $assignments = Assignment::with(['teacher', 'submissions'])
                ->orderByDesc('created_at')
                ->get();
            
            // Buat dummy teacher untuk admin/teknisi agar view tidak error
            if (!$teacher) {
                $teacher = (object)[
                    'name' => auth()->user()->full_name ?? 'Admin',
                    'token' => ''
                ];
            }
        }

        $organizations = \App\Models\Organization::where('is_active', true)->orderBy('name')->get();
        $classes       = \App\Models\LabClass::where('is_active', true)->orderBy('name')->get();

        return view('assignments.admin', compact('assignments', 'teacher', 'organizations', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_token'   => 'nullable|string',
            'title'           => 'required|string|max:200',
            'description'     => 'nullable|string',
            'subject_name'    => 'required|string|max:100',
            'class_names'     => 'required|array|min:1',
            'deadline'        => 'required|date|after:now',
            'attachment'      => 'nullable|file|max:20480',
            'organization_id' => 'required|exists:organizations,id',
        ], [
            'class_names.required' => 'Pilih minimal satu kelas.',
            'deadline.after'       => 'Deadline harus setelah waktu sekarang.',
        ]);

        $teacher = null;
        
        // Jika admin/teknisi, pilih guru mana? Wait, for now, let's handle both cases:
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'teknisi', 'staff', 'technician'])) {
            // For admin, we need to get a teacher, but wait let's check if teacher_token is provided
            if ($request->teacher_token) {
                $teacher = Teacher::where('token', strtoupper($request->teacher_token))
                    ->where('is_active', true)
                    ->firstOrFail();
            } else {
                // If no teacher_token, maybe first active teacher or throw error?
                // For now, let's get first active teacher as fallback, or throw error
                $teacher = Teacher::where('is_active', true)->first();
                if (!$teacher) {
                    return back()->withErrors(['teacher_token' => 'Pilih guru terlebih dahulu.']);
                }
            }
        } else {
            $teacher = Teacher::where('token', strtoupper($request->teacher_token))
                ->where('is_active', true)
                ->firstOrFail();
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentSize = null;

        // Ambil organization untuk slug
        $organization = \App\Models\Organization::findOrFail($request->organization_id);
        $orgSlug = $organization->slug;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            // Simpan ke attachments/{org_slug}/{class_name} untuk setiap kelas
            // Kita simpan satu file saja (untuk semua kelas) di path attachments/{org_slug}/
            // Atau jika ingin per kelas, kita bisa simpan per kelas tapi file sama
            $firstClassName = $request->class_names[0];
            $safeClassName = strtolower(str_replace([' ', '.', ',', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '[', ']', '{', '}', ';', ':', "'", '"', ',', '<', '>', '?', '/', '\\', '|', '`', '~'], '_', $firstClassName));
            
            $attachmentPath = $file->store("attachments/{$orgSlug}/{$safeClassName}", 'local');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = round($file->getSize() / 1024, 1) . ' KB';
        }

        foreach ($request->class_names as $className) {
            Assignment::create([
                'teacher_id'      => $teacher->id,
                'title'           => $request->title,
                'description'     => $request->description,
                'subject_name'    => $request->subject_name,
                'class_name'      => $className,
                'deadline'        => $request->deadline,
                'is_active'       => true,
                'organization_id' => $request->organization_id,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_size' => $attachmentSize,
            ]);
        }

        return back()->with('success', count($request->class_names) . ' tugas berhasil dibuat.');
    }

    public function destroy(Assignment $assignment)
    {
        // Hapus semua file submission
        $assignment->submissions->each(function ($sub) {
            if (Storage::disk('local')->exists($sub->file_path)) {
                Storage::disk('local')->delete($sub->file_path);
            }
        });

        // Hapus file lampiran jika ada
        if ($assignment->attachment_path && Storage::disk('local')->exists($assignment->attachment_path)) {
            Storage::disk('local')->delete($assignment->attachment_path);
        }

        $assignment->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function downloadAttachment(Assignment $assignment)
    {
        if (!$assignment->attachment_path) {
            return back()->withErrors(['error' => 'Tidak ada lampiran untuk tugas ini.']);
        }

        if (!Storage::disk('local')->exists($assignment->attachment_path)) {
            return back()->withErrors(['error' => 'File lampiran tidak ditemukan di server.']);
        }

        return Storage::disk('local')->download($assignment->attachment_path, $assignment->attachment_name);
    }

    public function downloadSubmission(AssignmentSubmission $submission)
    {
        if (!Storage::disk('local')->exists($submission->file_path)) {
            return back()->withErrors(['error' => 'File tidak ditemukan di server.']);
        }

        return Storage::disk('local')->download($submission->file_path, $submission->file_name);
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $request->validate([
            'grade'    => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'grade'    => $request->grade,
            'feedback' => $request->feedback,
            'status'   => 'graded',
        ]);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}