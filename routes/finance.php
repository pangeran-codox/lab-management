<?php

use App\Http\Controllers\Finance\AuthController;
use App\Http\Controllers\Finance\DashboardController;
use App\Http\Controllers\Finance\TransactionController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\LaporanController;
use App\Http\Controllers\Finance\WaSettingController;
use App\Http\Controllers\Finance\UserController;

// ── Auth (hanya untuk yang BELUM login finance) ───────────────
Route::middleware('finance.guest')->group(function () {
    Route::get('login',  [AuthController::class, 'showLogin'])->name('finance.login');
    Route::post('login', [AuthController::class, 'login'])->name('finance.login.post');
});

// Logout
Route::post('logout', [AuthController::class, 'logout'])
     ->name('finance.logout')
     ->middleware('finance.auth');

// ── Area Terproteksi (admin & bendahara) ──────────────────────
Route::middleware('finance.auth')->group(function () {
    Route::get('/',         [DashboardController::class, 'index'])->name('finance.dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('finance.dashboard.index');
    Route::get('laporan',   [LaporanController::class,   'index'])->name('finance.laporan.index');

    Route::resource('transactions', TransactionController::class)
         ->only(['index', 'create', 'store', 'show', 'destroy'])
         ->names([
             'index'   => 'finance.transactions.index',
             'create'  => 'finance.transactions.create',
             'store'   => 'finance.transactions.store',
             'show'    => 'finance.transactions.show',
             'destroy' => 'finance.transactions.destroy',
         ]);

    Route::resource('budgets', BudgetController::class)
         ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
         ->names([
             'index'   => 'finance.budgets.index',
             'create'  => 'finance.budgets.create',
             'store'   => 'finance.budgets.store',
             'edit'    => 'finance.budgets.edit',
             'update'  => 'finance.budgets.update',
             'destroy' => 'finance.budgets.destroy',
         ]);
});

// ── Area Khusus Admin ─────────────────────────────────────────
Route::middleware('finance.auth:admin')->group(function () {

    Route::get('wa-settings',       [WaSettingController::class, 'index'])->name('finance.wa-settings.index');
    Route::post('wa-settings',      [WaSettingController::class, 'update'])->name('finance.wa-settings.update');
    Route::post('wa-settings/test', [WaSettingController::class, 'test'])->name('finance.wa-settings.test');

	Route::get('wa-qr', function() {
    $apiKey = env('BAILEYS_API_KEY');
    $url    = env('BAILEYS_URL', 'http://170.1.0.46:3001');
    try {
        $status = \Illuminate\Support\Facades\Http::timeout(5)
            ->withHeaders(['X-API-Key' => $apiKey])
            ->get("{$url}/status")->json();
        if ($status['connected'] ?? false) {
            return response('<div style="font-family:sans-serif;text-align:center;margin-top:100px"><h2 style="color:green">✅ WhatsApp Sudah Terhubung!</h2><p>Tidak perlu scan QR.</p><a href="/finance/wa-settings" style="color:#166534">← Kembali ke Pengaturan WA</a></div>');
        }
        $qrData = \Illuminate\Support\Facades\Http::timeout(5)
            ->withHeaders(['X-API-Key' => $apiKey])
            ->get("{$url}/qr")->json();
        $qr = $qrData['qr'] ?? null;
        if (!$qr) {
            return response('<meta http-equiv="refresh" content="3"><div style="font-family:sans-serif;text-align:center;margin-top:100px"><h2>⏳ QR belum siap, tunggu...</h2></div>');
        }
        return view('finance.wa-qr', ['qr' => $qr]);
    } catch (\Exception $e) {
        return response('<div style="font-family:sans-serif;text-align:center;margin-top:100px"><h2 style="color:red">❌ Baileys tidak bisa diakses</h2><p>'.$e->getMessage().'</p></div>');
    }
	})->name('finance.wa-qr');

    Route::resource('users', UserController::class)
         ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
         ->names([
             'index'   => 'finance.users.index',
             'create'  => 'finance.users.create',
             'store'   => 'finance.users.store',
             'edit'    => 'finance.users.edit',
             'update'  => 'finance.users.update',
             'destroy' => 'finance.users.destroy',
         ]);
});
