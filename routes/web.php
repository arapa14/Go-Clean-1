<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
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

Route::middleware(['auth', 'isPetugas'])->group(function() {
    // Laporan Harian
    Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');
    Route::get('/riwayat', [ReportController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/data', [ReportController::class, 'getReports'])->name('report.getReports');

    // Pengaduan
    Route::post('/complain', [ComplaintController::class, 'store'])->name('complain.store');
    Route::get('/complain', [ComplaintController::class, 'complainPage'])->name('complainPage');
});

Route::middleware(['auth', 'isReviewerOrAdmin'])->group(function() {
    // Route untuk menampilkan halaman status laporan
    Route::get('/status', [ReportController::class, 'status'])->name('status');    
    Route::get('/getStatus', [ReportController::class, 'getStatus'])->name('getStatus');
    Route::post('/update-status', [ReportController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/approve-all', [ReportController::class, 'approveAll'])->name('approveAll');

    Route::get('/complaint', [ComplaintController::class, 'complaint'])->name('complaint');
    Route::get('/getComplaint', [ComplaintController::class, 'getComplaint'])->name('getComplaint');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    // Manage Users
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/getUsers', [UserController::class, 'getUsers'])->name('getUsers');

    // Route untuk menampilkan form edit user
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');

    // Route untuk mengupdate data user (menggunakan method PUT atau PATCH)
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');

    // Route untuk menghapus user
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.delete');
    Route::get('/switch/{id}', [UserController::class, 'switch'])->name('user.switch');

    // Manage Locations
    Route::get('/location', [LocationController::class, 'index'])->name('location');

    // Manage Settings
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
});
