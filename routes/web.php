<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InventoryPublicController;
use App\Http\Controllers\ScheduleAdminController;
use App\Http\Controllers\InventoryAdminController;
use App\Http\Controllers\RekapPublicController;
use App\Http\Controllers\AssignmentPublicController;
use App\Http\Controllers\AssignmentAdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\LabControlController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\LabClassController;
use App\Http\Controllers\ImportantScheduleController;

// ═══ PUBLIK ═══
Route::get('/', [ScheduleController::class, 'index'])->name('home');
Route::get('/jadwal-poll', [ScheduleController::class, 'poll'])->name('schedule.poll')->middleware('throttle:60,1');
Route::post('/booking', [ScheduleController::class, 'storeBooking'])->name('booking.store')->middleware('throttle:10,1');
Route::post('/booking-minggu', [ScheduleController::class, 'storeSundayBooking'])->name('sunday.booking.store')->middleware('throttle:10,1');
Route::get('/kelas', [ScheduleController::class, 'getClasses'])->name('classes.list')->middleware('throttle:30,1');
Route::get('/inventaris', [InventoryPublicController::class, 'index'])->name('inventory.public');
Route::get('/rekap', [RekapPublicController::class, 'index'])->name('rekap.public');

// ─── Tugas publik (dengan PIN) ────────────────────────────────────
Route::get('/tugas', [AssignmentPublicController::class, 'index'])->name('assignment.public');
Route::post('/tugas/pin', [AssignmentPublicController::class, 'verifyPin'])->name('assignment.pin.verify')->middleware('throttle:10,1');
Route::post('/tugas/ganti-kelas', [AssignmentPublicController::class, 'clearPin'])->name('assignment.pin.clear');
Route::get('/tugas/{assignment}', [AssignmentPublicController::class, 'show'])->name('assignment.show');
Route::post('/tugas/{assignment}/submit', [AssignmentPublicController::class, 'submit'])->name('assignment.submit')->middleware('throttle:10,1');

// ─── Tugas admin (pakai token guru, tanpa login) ──────────────────
Route::get('/tugas-admin', [AssignmentAdminController::class, 'index'])->name('assignment.admin');
Route::post('/tugas-admin', [AssignmentAdminController::class, 'store'])->name('assignment.store');
Route::delete('/tugas-admin/{assignment}', [AssignmentAdminController::class, 'destroy'])->name('assignment.destroy');
Route::post('/tugas-admin/submission/{submission}/grade', [AssignmentAdminController::class, 'gradeSubmission'])->name('assignment.grade');
Route::get('/tugas-admin/submission/{submission}/download', [AssignmentAdminController::class, 'downloadSubmission'])->name('assignment.download');
Route::get('/tugas/{assignment}/download-attachment', [AssignmentAdminController::class, 'downloadAttachment'])->name('assignment.download.attachment');

// Token guru (publik)
Route::post('/guru/verify-token', [TeacherController::class, 'verifyToken'])->name('teacher.verify');

// Fonnte webhook proxy → bot Python
Route::any('/fonnte-webhook', function(\Illuminate\Http\Request $request) {
    $response = \Illuminate\Support\Facades\Http::timeout(10)
        ->post(env('BOT_URL', 'http://170.1.0.9:5000') . '/api/webhook/fonnte', $request->all());
    return response()->json($response->json());
})->middleware('throttle:60,1');

// Lab control (publik, akses via link token)
Route::prefix('lab-control')->name('lab.')->group(function () {
    Route::get('/{token}', [LabControlController::class, 'control'])->name('control');
    Route::get('/{token}/status', [LabControlController::class, 'status'])->name('status');
    Route::post('/{token}/toggle', [LabControlController::class, 'toggleInternet'])->name('toggle');
    Route::post('/{token}/logout', [LabControlController::class, 'logout'])->name('logout');
});

// ═══ GUEST ═══
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
});

// ═══ AUTH ═══
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/schedule', fn() => view('dashboard'))->name('schedule.index');
    Route::get('/inventory', fn() => view('dashboard'))->name('inventory.index');
    Route::get('/procurement', fn() => view('dashboard'))->name('procurement.index');

    // Booking
    Route::patch('/booking/sunday/{id}/approve', [BookingController::class, 'approveSunday'])->name('booking.approve.sunday');
    Route::delete('/booking/sunday/{id}', [BookingController::class, 'destroySunday'])->name('booking.destroy.sunday');
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::patch('/booking/{booking}/approve', [BookingController::class, 'approve'])->name('booking.approve');
    Route::post('/booking/approve-group', [BookingController::class, 'approveGroup'])->name('booking.approve.group');
    Route::patch('/booking/{id}/reject', [BookingController::class, 'reject'])->name('booking.reject');
    Route::delete('/booking/{booking}', [BookingController::class, 'destroy'])->name('booking.destroy');

    // Jadwal admin
    Route::get('/jadwal-admin', [ScheduleAdminController::class, 'index'])->name('schedule.admin');
    Route::post('/jadwal-admin', [ScheduleAdminController::class, 'store'])->name('schedule.admin.store');
    Route::patch('/jadwal-admin/{schedule}', [ScheduleAdminController::class, 'update'])->name('schedule.admin.update');
    Route::delete('/jadwal-admin/{schedule}', [ScheduleAdminController::class, 'destroy'])->name('schedule.admin.destroy');

    // Inventaris admin
    Route::get('/inventaris-admin', [InventoryAdminController::class, 'index'])->name('inventory.admin');
    Route::post('/inventaris-admin', [InventoryAdminController::class, 'store'])->name('inventory.admin.store');
    Route::patch('/inventaris-admin/{inventory}', [InventoryAdminController::class, 'update'])->name('inventory.admin.update');
    Route::delete('/inventaris-admin/{inventory}', [InventoryAdminController::class, 'destroy'])->name('inventory.admin.destroy');

    // Guru
    Route::get('/guru', [TeacherController::class, 'index'])->name('teacher.index');
    Route::post('/guru', [TeacherController::class, 'store'])->name('teacher.store');
    Route::patch('/guru/{teacher}', [TeacherController::class, 'update'])->name('teacher.update');
    Route::delete('/guru/{teacher}', [TeacherController::class, 'destroy'])->name('teacher.destroy');

    // Sekolah & Kelas
    Route::get('/sekolah', [OrganizationController::class, 'index'])->name('organization.index');
    Route::post('/sekolah', [OrganizationController::class, 'store'])->name('organization.store');
    Route::patch('/sekolah/{organization}', [OrganizationController::class, 'update'])->name('organization.update');
    Route::delete('/sekolah/{organization}', [OrganizationController::class, 'destroy'])->name('organization.destroy');

    Route::get('/kelas-admin', [LabClassController::class, 'index'])->name('class.index');
    Route::post('/kelas-admin', [LabClassController::class, 'store'])->name('class.store');
    Route::patch('/kelas-admin/{class}', [LabClassController::class, 'update'])->name('class.update');
    Route::patch('/kelas-admin/{class}/reset-pin', [LabClassController::class, 'resetPin'])->name('class.reset-pin');
    Route::delete('/kelas-admin/{class}', [LabClassController::class, 'destroy'])->name('class.destroy');

    // Jadwal Penting
Route::get('/jadwal-penting', [ImportantScheduleController::class, 'index'])->name('important-schedule.index');
Route::get('/jadwal-penting/create', [ImportantScheduleController::class, 'create'])->name('important-schedule.create');
Route::post('/jadwal-penting', [ImportantScheduleController::class, 'store'])->name('important-schedule.store');
Route::get('/jadwal-penting/{importantSchedule}/edit', [ImportantScheduleController::class, 'edit'])->name('important-schedule.edit');
Route::patch('/jadwal-penting/{importantSchedule}', [ImportantScheduleController::class, 'update'])->name('important-schedule.update');
Route::delete('/jadwal-penting/{importantSchedule}', [ImportantScheduleController::class, 'destroy'])->name('important-schedule.destroy');

// API: cek slot terblokir (dipanggil dari halaman jadwal publik via AJAX)
Route::get('/api/jadwal-penting/blocked-slots', [ImportantScheduleController::class, 'blockedSlots'])->name('important-schedule.blocked-slots');
    // Lab control admin
    Route::post('/lab-control-admin/generate', [LabControlController::class, 'generateToken'])->name('lab.generate');
});