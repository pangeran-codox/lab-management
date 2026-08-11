<?php
namespace App\Http\Controllers;

use App\Events\AssignmentUpdated;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;
use App\Services\AssignmentSeriesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentAdminController extends Controller
{
    public function __construct(
        private AssignmentSeriesService $series,
    ) {}

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

    /**
     * Pastikan request punya akses yang valid.
     * Abort 403 kalau tidak punya akses sama sekali.
     * Return teacher instance (null jika admin/teknisi).
     */
    private function authorizeAccess(Request $request): ?Teacher
    {
        $access = $this->resolveAccess($request);

        if (!$access['role']) {
            abort(403, 'Akses ditolak. Token tidak valid atau sesi habis.');
        }

        return $access['teacher']; // null = admin/teknisi
    }

    /**
     * Pastikan guru hanya bisa akses tugasnya sendiri.
     * Admin/teknisi ($teacher = null) bisa akses semua.
     */
    private function authorizeAssignment(Assignment $assignment, ?Teacher $teacher): void
    {
        if ($teacher && $assignment->teacher_id !== $teacher->id) {
            abort(403, 'Kamu tidak memiliki akses ke tugas ini.');
        }
    }

    /**
     * Pastikan guru hanya bisa akses submission dari tugasnya sendiri.
     */
    private function authorizeSubmission(AssignmentSubmission $submission, ?Teacher $teacher): void
    {
        if ($teacher && $submission->assignment->teacher_id !== $teacher->id) {
            abort(403, 'Kamu tidak memiliki akses ke submission ini.');
        }
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
            $safeClassName = $this->safeSlug($firstClassName);

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

    /**
     * Buat tugas baru sebagai lanjutan dari tugas $previous (materi berkelanjutan).
     * Tugas lanjutan otomatis TERTUTUP — guru buka manual lewat toggleAccess().
     */
    public function storeContinuation(Request $request, Assignment $previous)
    {
        $teacher = $this->authorizeAccess($request);
        $this->authorizeAssignment($previous, $teacher);
        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'deadline'    => 'required|date|after:now',
            'attachment'  => 'nullable|file|max:20480',
        ], [
            'deadline.after' => 'Deadline harus setelah waktu sekarang.',
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $organization  = $previous->organization_id
                ? \App\Models\Organization::find($previous->organization_id)
                : null;
            $orgSlug       = $organization->slug ?? 'umum';
            $safeClassName = $this->safeSlug($previous->class_name);

            $attachmentPath = $file->store("attachments/{$orgSlug}/{$safeClassName}", 'local');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = round($file->getSize() / 1024, 1) . ' KB';
        }

        $assignment = $this->series->continueFrom($previous, [
            'title'           => $request->title,
            'description'     => $request->description,
            'deadline'        => $request->deadline,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_size' => $attachmentSize,
        ]);

        return back()->with('success', 'Tugas lanjutan "' . $assignment->title . '" berhasil dibuat. Tugas ini masih tertutup — klik "Buka Akses" saat siap ditampilkan ke siswa.');
    }

    /**
     * Toggle buka/tutup akses siswa untuk download file tugasnya sendiri.
     */
    public function toggleStudentDownload(Assignment $assignment)
    {
        $teacher = $this->authorizeAccess(request());
        $this->authorizeAssignment($assignment, $teacher);

        $assignment->update([
            'allow_student_download' => !$assignment->allow_student_download,
        ]);

        broadcast(new AssignmentUpdated($assignment->fresh(), 'updated'));

        $status = $assignment->allow_student_download ? 'dibuka' : 'ditutup';
        return back()->with('success', "Akses download siswa {$status}.");
    }

    /**
     * Toggle buka/tutup akses tugas ke siswa.
     */
    public function toggleAccess(Assignment $assignment)
    {
        $teacher = $this->authorizeAccess(request());
        $this->authorizeAssignment($assignment, $teacher);
        $this->series->toggleAccess($assignment);

        broadcast(new AssignmentUpdated($assignment->fresh(), 'access_changed'));

        return back()->with('success', $assignment->is_active
            ? 'Akses tugas dibuka untuk siswa.'
            : 'Akses tugas ditutup — tidak tampil di halaman siswa.');
    }

    /**
     * Buka kembali tugas yang sama untuk pertemuan berikutnya
     * (mis. tugas sudah expired/ditutup, dibuka lagi dengan deadline baru).
     */
    public function reopen(Request $request, Assignment $assignment)
    {
        $teacher = $this->authorizeAccess($request);
        $this->authorizeAssignment($assignment, $teacher);
        $request->validate([
            'deadline'       => 'required|date|after:now',
            'allow_resubmit' => 'nullable|boolean',
        ], [
            'deadline.after' => 'Deadline baru harus setelah waktu sekarang.',
        ]);

        $this->series->reopen(
            $assignment,
            Carbon::parse($request->deadline),
            $request->boolean('allow_resubmit')
        );

        broadcast(new AssignmentUpdated($assignment->fresh(), 'updated'));

        return back()->with('success', 'Tugas dibuka kembali untuk pertemuan berikutnya.');
    }

    public function destroy(Assignment $assignment)
    {
        $teacher = $this->authorizeAccess(request());
        $this->authorizeAssignment($assignment, $teacher);
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
        $teacher = $this->authorizeAccess(request());
        $this->authorizeAssignment($assignment, $teacher);
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
        $teacher = $this->authorizeAccess(request());
        $this->authorizeSubmission($submission, $teacher);
        if (!Storage::disk('local')->exists($submission->file_path)) {
            return back()->withErrors(['error' => 'File tidak ditemukan di server.']);
        }

        return Storage::disk('local')->download($submission->file_path, $submission->file_name);
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $teacher = $this->authorizeAccess($request);
        $this->authorizeSubmission($submission, $teacher);
        $request->validate([
            'grade'    => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'grade'    => $request->grade,
            'feedback' => $request->feedback,
            'status'   => 'graded',
        ]);

        broadcast(new AssignmentUpdated($submission->assignment, 'graded', [
            'submission_id' => $submission->id,
            'student_name'  => $submission->student_name,
            'grade'         => $request->grade,
        ]));

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    // ══════════════════════════════════════════════════════════════════
    // EXPORT EXCEL — nama + nilai siswa dalam format .xls (HTML table)
    // ══════════════════════════════════════════════════════════════════

    public function exportExcel(Assignment $assignment)
    {
        $teacher = $this->authorizeAccess(request());
        $this->authorizeAssignment($assignment, $teacher);
        $submissions = $assignment->submissions()
            ->orderBy('student_name')
            ->get();

        $safeTitle = $this->safeSlug($assignment->title);
        $safeClass = $this->safeSlug($assignment->class_name);
        $filename  = "nilai_{$safeClass}_{$safeTitle}.xls";

        // Format tanggal
        $now       = now()->translatedFormat('d F Y, H:i');
        $deadline  = $assignment->deadline->translatedFormat('d F Y, H:i');

        // Bangun HTML table — Excel bisa baca format ini langsung
        $html  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $html .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $html .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $html .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"';
        $html .= ' xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";

        // Style definisi
        $html .= '<Styles>' . "\n";
        $html .= '  <Style ss:ID="header"><Alignment ss:Horizontal="Center"/><Font ss:Bold="1" ss:Size="14"/></Style>' . "\n";
        $html .= '  <Style ss:ID="subheader"><Alignment ss:Horizontal="Left"/><Font ss:Bold="1" ss:Size="11"/></Style>' . "\n";
        $html .= '  <Style ss:ID="th"><Alignment ss:Horizontal="Center"/><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#00693E" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:Color="#cccccc" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td_no"><Alignment ss:Horizontal="Center"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td"><Alignment ss:Horizontal="Left"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td_num"><Alignment ss:Horizontal="Center"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td_good"><Alignment ss:Horizontal="Center"/><Font ss:Color="#166534"/><Interior ss:Color="#dcfce7" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td_mid"><Alignment ss:Horizontal="Center"/><Font ss:Color="#92400e"/><Interior ss:Color="#fef3c7" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td_bad"><Alignment ss:Horizontal="Center"/><Font ss:Color="#991b1b"/><Interior ss:Color="#fee2e2" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="td_empty"><Alignment ss:Horizontal="Center"/><Font ss:Color="#9ca3af" ss:Italic="1"/><Borders><Border ss:Position="Bottom" ss:Color="#e2e8f0" ss:Weight="1"/></Borders></Style>' . "\n";
        $html .= '  <Style ss:ID="footer"><Font ss:Italic="1" ss:Color="#6b7280"/></Style>' . "\n";
        $html .= '</Styles>' . "\n";

        $html .= '<Worksheet ss:Name="Nilai Tugas">' . "\n";
        $html .= '<Table ss:DefaultColumnWidth="120">' . "\n";

        // Lebar kolom
        $html .= '<Column ss:Width="40"/>';   // No
        $html .= '<Column ss:Width="200"/>';  // Nama
        $html .= '<Column ss:Width="120"/>';  // Kelas
        $html .= '<Column ss:Width="80"/>';   // Nilai
        $html .= '<Column ss:Width="90"/>';   // Status
        $html .= '<Column ss:Width="140"/>';  // Waktu Kumpul
        $html .= '<Column ss:Width="200"/>';  // Feedback

        // Row 1 — Judul
        $html .= '<Row ss:Height="30">';
        $html .= '<Cell ss:MergeAcross="6" ss:StyleID="header"><Data ss:Type="String">DAFTAR NILAI TUGAS – ' . strtoupper($assignment->title) . '</Data></Cell>';
        $html .= '</Row>' . "\n";

        // Row 2 — Info tugas
        $html .= '<Row ss:Height="20">';
        $html .= '<Cell ss:MergeAcross="6" ss:StyleID="subheader"><Data ss:Type="String">Kelas: ' . $assignment->class_name . '  |  Mata Pelajaran: ' . $assignment->subject_name . '  |  Guru: ' . ($assignment->teacher->name ?? '-') . '</Data></Cell>';
        $html .= '</Row>' . "\n";

        $html .= '<Row ss:Height="18">';
        $html .= '<Cell ss:MergeAcross="6" ss:StyleID="footer"><Data ss:Type="String">Deadline: ' . $deadline . '  |  Dicetak: ' . $now . '</Data></Cell>';
        $html .= '</Row>' . "\n";

        // Row kosong sebagai spacer
        $html .= '<Row ss:Height="8"><Cell><Data ss:Type="String"></Data></Cell></Row>' . "\n";

        // Header tabel
        $html .= '<Row ss:Height="24">';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">No</Data></Cell>';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">Nama Siswa</Data></Cell>';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">Kelas</Data></Cell>';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">Nilai</Data></Cell>';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">Status</Data></Cell>';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">Waktu Kumpul</Data></Cell>';
        $html .= '<Cell ss:StyleID="th"><Data ss:Type="String">Catatan Guru</Data></Cell>';
        $html .= '</Row>' . "\n";

        // Data rows
        $no = 1;
        foreach ($submissions as $sub) {
            // Tentukan style nilai berdasarkan angka
            if ($sub->grade === null) {
                $gradeStyle = 'td_empty';
                $gradeValue = '-';
                $gradeType  = 'String';
            } elseif ($sub->grade >= 75) {
                $gradeStyle = 'td_good';
                $gradeValue = (string) $sub->grade;
                $gradeType  = 'Number';
            } elseif ($sub->grade >= 60) {
                $gradeStyle = 'td_mid';
                $gradeValue = (string) $sub->grade;
                $gradeType  = 'Number';
            } else {
                $gradeStyle = 'td_bad';
                $gradeValue = (string) $sub->grade;
                $gradeType  = 'Number';
            }

            $statusLabel = $sub->status === 'graded' ? 'Sudah Dinilai' : 'Belum Dinilai';
            $statusStyle = $sub->status === 'graded' ? 'td_good' : 'td_mid';
            $waktu       = $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i') : '-';
            $feedback    = $sub->feedback ? htmlspecialchars($sub->feedback, ENT_XML1) : '';

            $html .= '<Row ss:Height="20">';
            $html .= '<Cell ss:StyleID="td_no"><Data ss:Type="Number">' . $no . '</Data></Cell>';
            $html .= '<Cell ss:StyleID="td"><Data ss:Type="String">' . htmlspecialchars($sub->student_name, ENT_XML1) . '</Data></Cell>';
            $html .= '<Cell ss:StyleID="td"><Data ss:Type="String">' . htmlspecialchars($sub->student_class, ENT_XML1) . '</Data></Cell>';
            $html .= '<Cell ss:StyleID="' . $gradeStyle . '"><Data ss:Type="' . $gradeType . '">' . $gradeValue . '</Data></Cell>';
            $html .= '<Cell ss:StyleID="' . $statusStyle . '"><Data ss:Type="String">' . $statusLabel . '</Data></Cell>';
            $html .= '<Cell ss:StyleID="td_num"><Data ss:Type="String">' . $waktu . '</Data></Cell>';
            $html .= '<Cell ss:StyleID="td"><Data ss:Type="String">' . $feedback . '</Data></Cell>';
            $html .= '</Row>' . "\n";

            $no++;
        }

        // Row kosong + summary
        $html .= '<Row ss:Height="8"><Cell><Data ss:Type="String"></Data></Cell></Row>' . "\n";

        $total    = $submissions->count();
        $graded   = $submissions->where('status', 'graded')->count();
        $avgGrade = $submissions->where('grade', '!=', null)->avg('grade');

        $html .= '<Row ss:Height="20">';
        $html .= '<Cell ss:MergeAcross="2" ss:StyleID="subheader"><Data ss:Type="String">RINGKASAN</Data></Cell>';
        $html .= '<Cell><Data ss:Type="String"></Data></Cell>';
        $html .= '</Row>' . "\n";

        $html .= '<Row ss:Height="18">';
        $html .= '<Cell ss:MergeAcross="1" ss:StyleID="footer"><Data ss:Type="String">Total Pengumpulan</Data></Cell>';
        $html .= '<Cell><Data ss:Type="String"></Data></Cell>';
        $html .= '<Cell ss:StyleID="td_num"><Data ss:Type="Number">' . $total . '</Data></Cell>';
        $html .= '</Row>' . "\n";

        $html .= '<Row ss:Height="18">';
        $html .= '<Cell ss:MergeAcross="1" ss:StyleID="footer"><Data ss:Type="String">Sudah Dinilai</Data></Cell>';
        $html .= '<Cell><Data ss:Type="String"></Data></Cell>';
        $html .= '<Cell ss:StyleID="td_good"><Data ss:Type="Number">' . $graded . '</Data></Cell>';
        $html .= '</Row>' . "\n";

        if ($avgGrade !== null) {
            $html .= '<Row ss:Height="18">';
            $html .= '<Cell ss:MergeAcross="1" ss:StyleID="footer"><Data ss:Type="String">Rata-rata Nilai</Data></Cell>';
            $html .= '<Cell><Data ss:Type="String"></Data></Cell>';
            $html .= '<Cell ss:StyleID="td_num"><Data ss:Type="Number">' . round($avgGrade, 1) . '</Data></Cell>';
            $html .= '</Row>' . "\n";
        }

        $html .= '</Table>' . "\n";
        $html .= '</Worksheet>' . "\n";
        $html .= '</Workbook>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function update(Request $request, Assignment $assignment)
    {
        $teacher = $this->authorizeAccess($request);
        $this->authorizeAssignment($assignment, $teacher);
        $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'subject_name'=> 'required|string|max:100',
            'deadline'    => 'required|date',
            'attachment'  => 'nullable|file|max:20480',
        ]);

        $data = [
            'title'        => $request->title,
            'description'  => $request->description,
            'subject_name' => $request->subject_name,
            'deadline'     => $request->deadline,
        ];

        if ($request->hasFile('attachment')) {
            // Hapus file lama kalau ada
            if ($assignment->attachment_path && Storage::disk('local')->exists($assignment->attachment_path)) {
                Storage::disk('local')->delete($assignment->attachment_path);
            }

            $org           = $assignment->organization_id ? \App\Models\Organization::find($assignment->organization_id) : null;
            $orgSlug       = $org->slug ?? 'umum';
            $safeClassName = $this->safeSlug($assignment->class_name);

            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store("attachments/{$orgSlug}/{$safeClassName}", 'local');
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_size'] = round($file->getSize() / 1024, 1) . ' KB';
        }

        // Jika deadline diubah, sinkronkan is_active juga
        // (buka kembali kalau deadline diperpanjang ke depan)
        if (\Carbon\Carbon::parse($request->deadline)->isFuture()) {
            $data['is_active'] = true;
        }

        $assignment->update($data);

        broadcast(new AssignmentUpdated($assignment->fresh(), 'updated'));

        return back()->with('success', 'Tugas "' . $assignment->title . '" berhasil diperbarui.');
    }

    // ══════════════════════════════════════════════════════════════════
    // DOWNLOAD ZIP — semua submission dalam satu tugas dikemas jadi ZIP
    // ══════════════════════════════════════════════════════════════════

    public function downloadZip(Assignment $assignment)
    {
        $teacher = $this->authorizeAccess(request());
        $this->authorizeAssignment($assignment, $teacher);
        $submissions = $assignment->submissions()
            ->orderBy('student_name')
            ->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Belum ada file yang dikumpulkan untuk tugas ini.');
        }

        $safeTitle = $this->safeSlug($assignment->title);
        $safeClass = $this->safeSlug($assignment->class_name);
        $zipName   = "tugas_{$safeClass}_{$safeTitle}.zip";
        $zipPath   = storage_path('app/temp/' . $zipName);

        // Pastikan folder temp ada
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0775, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $added = 0;
        foreach ($submissions as $sub) {
            $fullPath = Storage::disk('local')->path($sub->file_path);
            if (file_exists($fullPath)) {
                // Nama file di ZIP: NamaSiswa_NamaFile.ext
                $safeName = $this->safeSlug($sub->student_name);
                $ext      = pathinfo($sub->file_name, PATHINFO_EXTENSION);
                $zipEntry = "{$safeName}.{$ext}";
                $zip->addFile($fullPath, $zipEntry);
                $added++;
            }
        }

        $zip->close();

        if ($added === 0) {
            @unlink($zipPath);
            return back()->with('error', 'Tidak ada file yang bisa di-download (file mungkin sudah dihapus dari server).');
        }

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY SUBMISSION — hapus satu pengumpulan siswa
    // ══════════════════════════════════════════════════════════════════

    public function destroySubmission(AssignmentSubmission $submission)
    {
        $teacher = $this->authorizeAccess(request());
        $this->authorizeSubmission($submission, $teacher);
        // Hapus file dari storage
        if ($submission->file_path && Storage::disk('local')->exists($submission->file_path)) {
            Storage::disk('local')->delete($submission->file_path);
        }

        $name = $submission->student_name;
        $assignmentId = $submission->assignment_id;
        $assignment   = $submission->assignment;
        $submission->delete();

        broadcast(new AssignmentUpdated($assignment, 'submission_deleted', [
            'submission_id' => $submission->id,
            'student_name'  => $name,
        ]));

        return back()->with('success', 'Pengumpulan ' . $name . ' berhasil dihapus.');
    }

    private function safeSlug(string $value): string
    {
        return strtolower(str_replace(
            [' ', '.', ',', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '[', ']', '{', '}', ';', ':', "'", '"', ',', '<', '>', '?', '/', '\\', '|', '`', '~'],
            '_',
            $value
        ));
    }
}