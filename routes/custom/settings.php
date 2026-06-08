<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsUserController;
use App\Models\User;

Route::get('/user', [
    SettingsUserController::class,
    'show'
])
    ->name('settings.user.show')
    ->can('show', User::class);

Route::get('/user/{user}', [
    SettingsUserController::class,
    'edit'
])
    ->name('settings.user.edit')
    ->can('edit', User::class);

Route::put('/user/{user}', [
    SettingsUserController::class,
    'update'
])
    ->name('settings.user.update')
    ->can('update,user');
