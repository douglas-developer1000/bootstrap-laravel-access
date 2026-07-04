<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImpersonateController;
use App\Models\User;
use Illuminate\Support\Str;

Route::post('/signout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');
Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware(['auth', 'verified']);
Route::get('/storage/app/{folder}/{filename}', [ImageController::class, 'find'])
    ->name('user.photo.show')
    ->middleware('auth');

Route::post('/impersonate/logout', [ImpersonateController::class, 'logout'])
    ->name('impersonate.logout')
    ->middleware('auth');

Route::post('/impersonate/{user}', [ImpersonateController::class, 'login'])
    ->name('impersonate.login')
    ->middleware([Str::of('can:beSuperAdmin,')->append(User::class)->toString()]);
