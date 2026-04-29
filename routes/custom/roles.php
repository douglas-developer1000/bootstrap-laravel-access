<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Libraries\Enums\RoleNameEnum;

Route::middleware(['role:' . RoleNameEnum::SUPER_ADMIN->value])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/{role}/edit/', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/group', [RoleController::class, 'removeGroup'])->name('roles.group.destroy');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/{role}/attach', [RoleController::class, 'attach'])->name('roles.attach');
    Route::post('/{role}/attach/{permission}', [RoleController::class, 'bind'])->name('roles.bind');
    Route::post('/{role}/detach/{permission}', [RoleController::class, 'unbind'])->name('roles.unbind');
});
