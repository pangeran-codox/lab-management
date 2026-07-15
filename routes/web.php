<?php
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
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
use App\Http\Controllers\InventoryMaintenanceController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\UsageReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MikroTikSettingController;

// ═══ PUBLIK ═══
Route::get('/', [ScheduleController::class, 'index'])->name('home');
Route::get('/jadwal-poll', [ScheduleController::class, 'poll'])->name('schedule.poll')->middleware('throttle:60,1');
Route::post('/booking', [ScheduleController::class, 'storeBooking'])->name('booking.store')->middleware('throttle:10,1');
Route::post('/booking-minggu', [ScheduleController::class, 'storeSundayBooking'])->name('sunday.booking.store')->middleware('throttle:10,1');
Route::get('/kelas', [ScheduleController::class, 'getClasses'])->name('classes.list')->middleware('throttle:30,1');

// ─── Laporan ───────────────────────────────────
Route::get('/laporan', function() {
    return view('reports.public');
})->name('reports.public');

Route::get('/inventaris', [InventoryPublicController::class, 'index'])->name('inventory.public');
Route::get('/inventaris/export/pdf', [InventoryPublicController::class, 'exportPdf'])->name('inventory.public.pdf');
Route::get('/rekap', [RekapPublicController::class, 'index'])->name('rekap.public');
Route::get('/rekap/export/pdf', [RekapPublicController::class, 'exportPdf'])->name('rekap.public.pdf');

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
Route::get('/tugas-admin/logout', function() {
    session()->forget('teacher_token');
    return redirect()->route('assignment.admin');
})->name('assignment.admin.logout');

// Token guru (publik)
Route::post('/guru/verify-token', [TeacherController::class, 'verifyToken'])->name('teacher.verify');

// Fonnte webhook proxy → bot Python
Route::any('/fonnte-webhook', function(\Illuminate\Http\Request $request) {
    $response = \Illuminate\Support\Facades\Http::timeout(10)
        ->post(env('BOT_URL', 'http://170.1.0.9:5000') . '/api/webhook/fonnte', $request->all());
    return response()->json($response->json());
})->middleware('throttle:60,1');

// Simpan ukuran font kop laporan — publik karena diakses dari halaman editor tanpa login
Route::post('/settings/kop-size', [SettingController::class, 'saveKopSize'])->name('settings.kop-size')->middleware('throttle:10,1');

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [DashboardController::class, 'index'])->name('schedule.index');
    Route::get('/inventory', fn() => view('dashboard'))->name('inventory.index');
    Route::get('/procurement', fn() => view('dashboard'))->name('procurement.index');

    // Booking
    Route::patch('/booking/sunday/{id}/approve', [BookingController::class, 'approveSunday'])->name('booking.approve.sunday');
    Route::delete('/booking/sunday/{id}', [BookingController::class, 'destroySunday'])->name('booking.destroy.sunday');
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::patch('/booking/{booking}/approve', [BookingController::class, 'approve'])->name('booking.approve');
    Route::post('/booking/approve-group', [BookingController::class, 'approveGroup'])->name('booking.approve.group');
    Route::post('/booking/reject-group', [BookingController::class, 'rejectGroup'])->name('booking.reject.group');
    Route::patch('/booking/{id}/reject', [BookingController::class, 'reject'])->name('booking.reject');
    Route::delete('/booking/{booking}', [BookingController::class, 'destroy'])->name('booking.destroy');

    // Jadwal admin
    Route::get('/jadwal-admin', [ScheduleAdminController::class, 'index'])->name('schedule.admin');
    Route::post('/jadwal-admin', [ScheduleAdminController::class, 'store'])->name('schedule.admin.store');
    Route::get('/jadwal-admin/export/{resource}', [ScheduleAdminController::class, 'export'])->name('schedule.admin.export');
    Route::patch('/jadwal-admin/{schedule}', [ScheduleAdminController::class, 'update'])->name('schedule.admin.update');
    Route::delete('/jadwal-admin/{schedule}', [ScheduleAdminController::class, 'destroy'])->name('schedule.admin.destroy');

   // Inventaris admin
    Route::get('/inventaris-admin', [InventoryAdminController::class, 'index'])->name('inventory.admin');
    Route::post('/inventaris-admin', [InventoryAdminController::class, 'store'])->name('inventory.admin.store');
    Route::patch('/inventaris-admin/{inventory}', [InventoryAdminController::class, 'update'])->name('inventory.admin.update');
    Route::delete('/inventaris-admin/{inventory}', [InventoryAdminController::class, 'destroy'])->name('inventory.admin.destroy');
    Route::patch('/inventaris-admin/{inventory}/quick-update', [InventoryAdminController::class, 'quickUpdate'])->name('inventory.admin.quick-update');
    
    // Log Perbaikan Inventaris
    Route::get('/inventaris-maintenance', [InventoryMaintenanceController::class, 'index'])->name('inventory.maintenance.index');
    Route::post('/inventaris-maintenance', [InventoryMaintenanceController::class, 'store'])->name('inventory.maintenance.store');
    Route::delete('/inventaris-maintenance/{log}', [InventoryMaintenanceController::class, 'destroy'])->name('inventory.maintenance.destroy');

    // Laporan Inventaris
    Route::get('/inventaris-report/pdf', [InventoryReportController::class, 'exportPdf'])->name('inventory.report.pdf');
    // Route::get('/inventaris-report/excel', [InventoryReportController::class, 'exportExcel'])->name('inventory.report.excel');
    
    // Laporan Penggunaan Lab
    Route::get('/laporan-penggunaan', [UsageReportController::class, 'index'])->name('reports.usage.index');
    Route::get('/laporan-penggunaan/generate', [UsageReportController::class, 'generate'])->name('reports.usage.generate');
    
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

    // Pengelolaan Absen Jadwal
    Route::post('/schedule-absences', [\App\Http\Controllers\ScheduleAbsenceController::class, 'store'])->name('schedule-absences.store');
    Route::delete('/schedule-absences/{absence}', [\App\Http\Controllers\ScheduleAbsenceController::class, 'destroy'])->name('schedule-absences.destroy');

    // Pengelolaan Pengguna
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Pengaturan Sistem
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/upload-logo',   [SettingController::class, 'uploadLogo'])->name('settings.upload-logo');
    Route::delete('/settings/delete-logo', [SettingController::class, 'deleteLogo'])->name('settings.delete-logo');
    Route::post('/settings/identitas',     [SettingController::class, 'saveIdentitas'])->name('settings.identitas');
    Route::post('/settings/wa',            [SettingController::class, 'saveWa'])->name('settings.wa');
    Route::post('/settings/booking',       [SettingController::class, 'saveBooking'])->name('settings.booking');
    Route::post('/settings/lab-control',   [SettingController::class, 'saveLabControl'])->name('settings.lab-control');
    Route::post('/settings/laporan',       [SettingController::class, 'saveLaporan'])->name('settings.laporan');
    Route::prefix('settings/mikrotik')->name('mikrotik.')->group(function () {
        Route::get('/',                                  [MikroTikSettingController::class, 'index'])->name('settings.index');
        // Devices
        Route::post('/devices',                          [MikroTikSettingController::class, 'storeDevice'])->name('device.store');
        Route::patch('/devices/{device}',                [MikroTikSettingController::class, 'updateDevice'])->name('device.update');
        Route::delete('/devices/{device}',               [MikroTikSettingController::class, 'destroyDevice'])->name('device.destroy');
        Route::post('/devices/{device}/test',            [MikroTikSettingController::class, 'testConnection'])->name('test');
        // Labs
        Route::post('/devices/{device}/labs',            [MikroTikSettingController::class, 'storeLab'])->name('lab.store');
        Route::patch('/labs/{lab}',                      [MikroTikSettingController::class, 'updateLab'])->name('lab.update');
        Route::delete('/labs/{lab}',                     [MikroTikSettingController::class, 'destroyLab'])->name('lab.destroy');
        // Teknisi
        Route::post('/labs/{lab}/teknisi',               [MikroTikSettingController::class, 'assignTeknisi'])->name('lab.teknisi.assign');
        Route::delete('/labs/{lab}/teknisi',             [MikroTikSettingController::class, 'unassignTeknisi'])->name('lab.teknisi.unassign');
    });
});