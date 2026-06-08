<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleUserController;
use App\Http\Controllers\PermissionUserController;
use App\Models\User;
use Illuminate\Support\Str;

Route::middleware([Str::of('can:beSuperAdmin,')->append(User::class)->toString()])->group(function () {
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

    Route::get('/{user}/attach/roles', [RoleUserController::class, 'getRoles'])->name('users.attach.roles');
    Route::post('/{user}/attach/roles/group', [RoleUserController::class, 'bindRoleGroup'])->name('users.bind.roles.group');
    Route::post('/{user}/attach/roles/{role}', [RoleUserController::class, 'bindRole'])->name('users.bind.roles');
    Route::delete('/{user}/detach/roles/group', [RoleUserController::class, 'unbindRoleGroup'])->name('users.unbind.roles.group');
    Route::delete('/{user}/detach/roles/{role}', [RoleUserController::class, 'unbindRole'])->name('users.unbind.roles');

    Route::get('/{user}/attach/permissions', [PermissionUserController::class, 'getDirectPermissions'])->name('users.attach.permissions');
    Route::post('/{user}/attach/permissions/group', [PermissionUserController::class, 'bindDirectPermissionGroup'])->name('users.bind.permissions.group');
    Route::post('/{user}/attach/permissions/{permission}', [PermissionUserController::class, 'bindDirectPermission'])->name('users.bind.permissions');
    Route::delete('/{user}/detach/permissions/group', [PermissionUserController::class, 'unbindDirectPermissionGroup'])->name('users.unbind.permissions.group');
    Route::delete('/{user}/detach/permissions/{permission}', [PermissionUserController::class, 'unbindDirectPermission'])->name('users.unbind.permissions');
});
