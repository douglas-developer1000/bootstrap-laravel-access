<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::post('/signout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::view('/dashboard', 'pages.dashboard')->name('dashboard')->middleware(['auth', 'verified']);
Route::get('/storage/app/{folder}/{filename}', [ImageController::class, 'find'])->name('user.photo.show')->middleware('auth');
