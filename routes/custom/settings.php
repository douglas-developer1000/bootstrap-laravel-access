<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsUserController;

Route::prefix('settings')->group(function () {
    Route::get('/user', [SettingsUserController::class, 'show'])->name('settings.user.show');
    Route::get('/user/{user}', [SettingsUserController::class, 'edit'])->name('settings.user.edit');
    Route::put('/user/{user}', [SettingsUserController::class, 'update'])->name('settings.user.update');
})->middleware('can:user');
