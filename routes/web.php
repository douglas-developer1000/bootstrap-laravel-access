<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/signin', 'pages.signin')->name('login');
    Route::post('/signin', [AuthController::class, 'login'])->name('login.post');

    Route::view('/forgot-password', 'pages.f-password')->name('password.request');
    Route::view('/register-request', 'pages.r-request')->name('register.request');
});
Route::middleware('auth')->group(function () {
    Route::post('/signout', [AuthController::class, 'logout'])->name('logout');
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::get('/{permission}/edit/', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    })->middleware('can:super-admin');

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/{role}/edit/', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/{role}/attach', [RoleController::class, 'attach'])->name('roles.attach');
        Route::post('/{role}/attach/{permission}', [RoleController::class, 'bind'])->name('roles.bind');
        Route::post('/{role}/detach/{permission}', [RoleController::class, 'unbind'])->name('roles.unbind');
    })->middleware('can:super-admin');

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit/', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/{user}/attach/roles', [UserController::class, 'attachRoles'])->name('users.attach.roles');
        Route::post('/{user}/attach/roles/{role}', [UserController::class, 'bindRole'])->name('users.bind.roles');
        Route::post('/{user}/detach/roles/{role}', [UserController::class, 'unbindRole'])->name('roles.unbind.roles');

        Route::get('/{user}/attach/permissions', [UserController::class, 'attachDirectPermissions'])->name('users.attach.permissions');
        Route::post('/{user}/attach/permissions/{permission}', [UserController::class, 'bindDirectPermission'])->name('users.bind.permissions');
        Route::post('/{user}/detach/permissions/{permission}', [UserController::class, 'unbindDirectPermission'])->name('roles.unbind.permissions');
    })->middleware('can:super-admin');
});
