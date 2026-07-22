<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/signin', 'pages.signin')->name('login');
    Route::post('/signin', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'screen'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'ask'])->name('password.email')->middleware([
        'throttle:10,1',
        'timingProtect',
    ]);

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update')->middleware('throttle:10,1');

    Route::get('/signup', [UserController::class, 'createByUser'])->name('guest.users.create');
    Route::post('/signup', [UserController::class, 'storeByUser'])->name('guest.users.store')->middleware('throttle:10,1');
});
