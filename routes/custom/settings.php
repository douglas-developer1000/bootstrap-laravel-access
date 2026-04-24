<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsUserController;
use App\Libraries\Enums\RoleNameEnum;

Route::middleware(['role:' . RoleNameEnum::USER->value])->group(function () {
    Route::get('/user', [SettingsUserController::class, 'show'])->name('settings.user.show');
    Route::get('/user/{user}', [SettingsUserController::class, 'edit'])->name('settings.user.edit');
    Route::put('/user/{user}', [SettingsUserController::class, 'update'])->name('settings.user.update');
});
