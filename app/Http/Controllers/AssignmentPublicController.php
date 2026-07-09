<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Organization;
use App\Models\LabClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AssignmentPublicController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    // INDEX — Halaman utama, cek PIN dari session
    // ══════════════════════════════════════════════════════════════════

    public function index()
    {
        // Cek apakah sudah ada PIN valid di session
        $activeClass = null;
        $assignments = collect();

        $classPin = session('assignment_class_pin');

        if ($classPin) {
            $activeClass = Cache::remember('class_pin_' . $classPin, 300, function () use ($classPin) {
                return LabClass::where('pin', $classPin)
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                    ->with('organization')
                    ->first(['id', 'name', 'organization_id', 'pin']);
            });

            // PIN tidak valid atau kelas dihapus — hapus session
            if (!$activeClass) {
                session()->forget('assignment_class_pin');
            } else {
                $assignments = $this->getAssignmentsForClass($activeClass);
            }
        }

        return view('assignments.public', compact('activeClass', 'assignments'));
    }

    // ══════════════════════════════════════════════════════════════════
    // VERIFY PIN — POST /tugas/pin
    // ══════════════════════════════════════════════════════════════════

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6|regex:/^\d{6}$/',
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.size'     => 'PIN harus 6 digit.',
            'pin.regex'    => 'PIN hanya boleh angka.',
        ]);

        $pin = $request->pin;

        $class = LabClass::where('pin', $pin)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->with('organization')
            ->first(['id', 'name', 'organization_id', 'pin']);

        if (!$class) {
            return back()->withErrors(['pin' => 'PIN salah atau kelas tidak ditemukan.'])->withInput();
        }

        // Simpan PIN ke session
        session(['assignment_class_pin' => $pin]);

        return redirect()->route('assignment.public');
    }

    // ══════════════════════════════════════════════════════════════════
    // CLEAR PIN — POST /tugas/ganti-kelas
    // ══════════════════════════════════════════════════════════════════

    public function clearPin()
    {
        session()->forget('assignment_class_pin');
        return redirect()->route('assignment.public');
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════════════════════════════════

    public function show(Assignment $assignment)
    {
        if (!$assignment->is_active) abort(404);

        // Validasi — kelas yang membuka harus sesuai PIN di session
        $classPin    = session('assignment_class_pin');
        $activeClass = $classPin ? LabClass::where('pin', $classPin)->with('organization')->first(['id', 'name', 'organization_id']) : null;

        if (!$activeClass || $activeClass->name !== $assignment->class_name) {
            return redirect()->route('assignment.public')
                ->withErrors(['pin' => 'Akses tidak diizinkan. Silakan masukkan PIN kelas yang sesuai.']);
        }

        $submissions = $assignment->submissions()
            ->orderByDesc('submitted_at')
            ->get([
                'id', 'assignment_id', 'student_name', 'student_class',
                'file_name', 'file_size', 'file_ext', 'status', 'submitted_at',
            ]);

        return view('assignments.show', compact('assignment', 'submissions', 'activeClass'));
    }

    // ══════════════════════════════════════════════════════════════════
    // SUBMIT
    // ══════════════════════════════════════════════════════════════════

    public function submit(Request $request, Assignment $assignment)
    {
        if ($assignment->isExpired()) {
            return back()->withErrors(['error' => 'Deadline sudah lewat.']);
        }

        // Validasi kelas sesuai PIN
        $classPin    = session('assignment_class_pin');
        $activeClass = $classPin ? LabClass::where('pin', $classPin)->with('organization')->first(['id', 'name', 'organization_id']) : null;

        if (!$activeClass || $activeClass->name !== $assignment->class_name) {
            return redirect()->route('assignment.public')
                ->withErrors(['pin' => 'Sesi kelas tidak valid. Masukkan PIN kembali.']);
        }

        $request->validate([
            'student_name' => 'required|string|max:100',
            'file'         => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240',
        ], [
            'file.mimes' => 'File harus berformat PDF, Word, PowerPoint, Excel, ZIP, atau RAR.',
            'file.max'   => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('file');
        
        // Ambil organization dan kelas untuk struktur folder
        $activeClassOrg = $activeClass->organization;
        $orgSlug = $activeClassOrg->slug;
        $safeClassName = strtolower(str_replace([' ', '.', ',', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '[', ']', '{', '}', ';', ':', "'", '"', ',', '<', '>', '?', '/', '\\', '|', '`', '~'], '_', $activeClass->name));
        
        $path = $file->store("submissions/{$orgSlug}/{$safeClassName}/{$assignment->id}", 'local');

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_name'  => $request->student_name,
            'student_class' => $activeClass->name, // ← dari session, bukan input user
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'file_size'     => round($file->getSize() / 1024, 1) . ' KB',
            'file_ext'      => $file->getClientOriginalExtension(),
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        Cache::forget('active_assignments_class_' . $activeClass->id);

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function getAssignmentsForClass(LabClass $class)
    {
        return Cache::remember('active_assignments_class_' . $class->id, 120, function () use ($class) {
            return Assignment::with(['teacher:id,name'])
                ->withCount('submissions')
                ->where('is_active', true)
                ->where('class_name', $class->name)   // filter per kelas
                ->orderBy('deadline')
                ->get([
                    'id', 'title', 'description', 'deadline',
                    'teacher_id', 'class_name', 'is_active',
                    'attachment_path', 'attachment_name',
                ]);
        });
    }
}