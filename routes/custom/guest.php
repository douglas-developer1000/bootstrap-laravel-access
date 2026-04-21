<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RegisterOrderController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/signin', 'pages.signin')->name('login');
    Route::post('/signin', [AuthController::class, 'login'])->name('login.post');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'screen'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'ask'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');

    Route::get('/register-order', [RegisterOrderController::class, 'create'])->name('register.orders.create');
    Route::post('/register-order', [RegisterOrderController::class, 'store'])->name('register.orders.store');
});

Route::get('/signup', [UserController::class, 'createSigned'])->name('guest.users.create')->middleware(['signed', 'guest']);
Route::post('/signup', [UserController::class, 'storeSigned'])->name('guest.users.store')->middleware('guest');
