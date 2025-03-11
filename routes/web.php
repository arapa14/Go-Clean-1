<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/', [AuthController::class, 'index'])->name('auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Role
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// API
Route::get('/server-time', [AuthController::class, 'getServerTime']);
Route::get('/switch/{id}', [UserController::class, 'switchAccount'])->name('switchAccount');
Route::get('/switch-back', [UserController::class, 'switchBack'])->name('switchBack');


// Tampilkan halaman form lupa password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
// Proses pengiriman link reset password ke email
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Tampilkan halaman reset password (diakses melalui link email)
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
// Proses update password
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');


Route::middleware('auth')->group(function () {
    // Route untuk update password jika default
    Route::post('/update-password', [UserController::class, 'updatePassword'])->name('update.password');
});

Route::middleware(['auth', 'isPetugas'])->group(function () {
    // Laporan Harian
    Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');
    Route::get('/riwayat', [ReportController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/data', [ReportController::class, 'getReports'])->name('report.getReports');

    // Pengaduan
    Route::post('/complain', [ComplaintController::class, 'store'])->name('complain.store');
    Route::get('/complain', [ComplaintController::class, 'complainPage'])->name('complainPage');
});

Route::middleware(['auth', 'isReviewerOrAdmin'])->group(function () {
    // Route untuk menampilkan halaman status laporan
    Route::get('/status', [ReportController::class, 'status'])->name('status');
    Route::get('/getStatus', [ReportController::class, 'getStatus'])->name('getStatus');
    Route::post('/update-status', [ReportController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/approve-all', [ReportController::class, 'approveAll'])->name('approveAll');

    Route::get('/complaint', [ComplaintController::class, 'complaint'])->name('complaint');
    Route::get('/getComplaint', [ComplaintController::class, 'getComplaint'])->name('getComplaint');
    Route::post('/update-complaint', [ComplaintController::class, 'updateStatus'])->name('updateComplaint');
    Route::post('/mark-all-noted', [ComplaintController::class, 'markAllNoted'])->name('markAllNoted');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    // Manage Users
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/getUsers', [UserController::class, 'getUsers'])->name('getUsers');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.delete');

    // Manage Locations
    Route::get('/location', [LocationController::class, 'index'])->name('location');
    Route::get('/getLocations', [LocationController::class, 'getLocations'])->name('getLocations');
    Route::post('/location', [LocationController::class, 'store'])->name('location.store');
    Route::get('/location/{id}/edit', [LocationController::class, 'edit'])->name('location.edit');
    Route::put('/location/{id}', [LocationController::class, 'update'])->name('location.update');
    Route::delete('/location/{id}', [LocationController::class, 'destroy'])->name('location.delete');

    // Manage Settings
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting/update', [SettingController::class, 'update'])->name('setting.update');
});

// Error 403: Forbidden
Route::get('/error-403', function () {
    abort(403, 'Akses ditolak.');
});

// Error 404: Not Found
Route::get('/error-404', function () {
    abort(404, 'Halaman tidak ditemukan.');
});

// Error 419: Page Expired
Route::get('/error-419', function () {
    abort(419, 'Sesi telah kedaluwarsa.');
});

// Error 500: Internal Server Error
Route::get('/error-500', function () {
    abort(500, 'Terjadi kesalahan pada server.');
});

// Error 502: Bad Gateway
Route::get('/error-502', function () {
    abort(502, 'Bad Gateway.');
});
