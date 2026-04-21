<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyEmailController;

Route::get('/email/verify', [VerifyEmailController::class, 'verify'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'handle'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
