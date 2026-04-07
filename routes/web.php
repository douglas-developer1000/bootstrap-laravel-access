<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
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

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/permissions/{permission}/edit/', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
});
