<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
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
    });
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
    });
});
