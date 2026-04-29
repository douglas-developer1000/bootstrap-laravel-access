<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Libraries\Enums\RoleNameEnum;

Route::middleware(['role:' . RoleNameEnum::SUPER_ADMIN->value])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit/', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/group', [UserController::class, 'removeGroup'])->name('users.group.destroy');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/trashed', [UserController::class, 'index'])->name('users.trashed.index');
    Route::delete('/trashed/{user}', [UserController::class, 'destroyTrashed'])->name('users.trashed.destroy');
    Route::post('/trashed/{user}/restore', [UserController::class, 'restore'])->name('users.trashed.restore');
    Route::post('/trashed/restore/group', [UserController::class, 'restoreGroup'])->name('users.trashed.group.restore');

    Route::get('/{user}/attach/roles', [UserController::class, 'attachRoles'])->name('users.attach.roles');
    Route::post('/{user}/attach/roles/{role}', [UserController::class, 'bindRole'])->name('users.bind.roles');
    Route::post('/{user}/detach/roles/{role}', [UserController::class, 'unbindRole'])->name('roles.unbind.roles');

    Route::get('/{user}/attach/permissions', [UserController::class, 'attachDirectPermissions'])->name('users.attach.permissions');
    Route::post('/{user}/attach/permissions/{permission}', [UserController::class, 'bindDirectPermission'])->name('users.bind.permissions');
    Route::post('/{user}/detach/permissions/{permission}', [UserController::class, 'unbindDirectPermission'])->name('roles.unbind.permissions');
});
