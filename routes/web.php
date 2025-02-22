<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])->name('auth.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/duri: ashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');

//API
Route::get('/server-time', [AuthController::class, 'getServerTime']);