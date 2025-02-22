<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])->name('auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/riwayat', [ReportController::class, 'riwayat'])->name('riwayat');
Route::get('/riwayat/data', [ReportController::class, 'getReports'])->name('report.getReports');
Route::post('/laporan', [ReportController::class, 'store'])->name('laporan.store');

Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');

//API
Route::get('/server-time', [AuthController::class, 'getServerTime']);
