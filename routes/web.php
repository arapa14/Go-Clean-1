<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/', [AuthController::class, 'index'])->name('auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Role
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// API
Route::get('/server-time', [AuthController::class, 'getServerTime']);

Route::middleware(['auth', 'isPetugas'])->group(function() {
    // Laporan Harian
    Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');
    Route::get('/riwayat', [ReportController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/data', [ReportController::class, 'getReports'])->name('report.getReports');

    // Pengaduan
    Route::post('/complain', [ComplaintController::class, 'store'])->name('complain.store');
    Route::get('/complain', [ComplaintController::class, 'complainPage'])->name('complainPage');
});

Route::middleware(['auth', 'isReviewer'])->group(function() {
    // Route untuk menampilkan halaman status laporan
    Route::get('/status', [ReportController::class, 'status'])->name('status');    
    Route::get('/getStatus', [ReportController::class, 'getStatus'])->name('getStatus');
    Route::post('/update-status', [ReportController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/approve-all', [ReportController::class, 'approveAll'])->name('approveAll');

    Route::get('/complaint', [ComplaintController::class, 'complaint'])->name('complaint');
    Route::get('/getComplaint', [ComplaintController::class, 'getComplaint'])->name('getComplaint');
});
