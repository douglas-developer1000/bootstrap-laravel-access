<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Libraries\Enums\RoleNameEnum;

Route::middleware(['role:' . RoleNameEnum::SUPER_ADMIN->value])->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/{permission}/edit/', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/group', [PermissionController::class, 'removeGroup'])->name('permissions.group.destroy');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
});
