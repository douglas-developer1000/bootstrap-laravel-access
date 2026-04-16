<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegisterApprovalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterOrderController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SettingsUserController;
use App\Http\Controllers\VerifyEmailController;

Route::middleware('guest')->group(function () {
    Route::view('/', 'pages.home')->name('home');
    Route::view('/signin', 'pages.signin')->name('login');
    Route::post('/signin', [AuthController::class, 'login'])->name('login.post');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'screen'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'ask'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');

    Route::get('/register-order', [RegisterOrderController::class, 'create'])->name('register.orders.create');
    Route::post('/register-order', [RegisterOrderController::class, 'store'])->name('register.orders.store');
});

Route::get('/signup', [UserController::class, 'createSigned'])->name('guest.users.create')->middleware(['signed', 'guest']);
Route::post('/signup', [UserController::class, 'storeSigned'])->name('guest.users.store')->middleware('guest');

Route::post('/signout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
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
        Route::get('/trashed', [UserController::class, 'index'])->name('users.trashed.index');
        Route::delete('/trashed/{user}', [UserController::class, 'destroyTrashed'])->name('users.trashed.destroy');
        Route::post('/trashed/{user}/restore', [UserController::class, 'restore'])->name('users.trashed.restore');

        Route::get('/{user}/attach/roles', [UserController::class, 'attachRoles'])->name('users.attach.roles');
        Route::post('/{user}/attach/roles/{role}', [UserController::class, 'bindRole'])->name('users.bind.roles');
        Route::post('/{user}/detach/roles/{role}', [UserController::class, 'unbindRole'])->name('roles.unbind.roles');

        Route::get('/{user}/attach/permissions', [UserController::class, 'attachDirectPermissions'])->name('users.attach.permissions');
        Route::post('/{user}/attach/permissions/{permission}', [UserController::class, 'bindDirectPermission'])->name('users.bind.permissions');
        Route::post('/{user}/detach/permissions/{permission}', [UserController::class, 'unbindDirectPermission'])->name('roles.unbind.permissions');
    })->middleware('can:super-admin');

    Route::prefix('register-orders')->group(function () {
        Route::get('/', [RegisterOrderController::class, 'index'])->name('register.orders.index');
        Route::delete('/{order}', [RegisterOrderController::class, 'destroy'])->name('register.orders.destroy');
        Route::delete('/{order}/approval', [RegisterOrderController::class, 'approve'])->name('register.orders.approve');
    })->middleware('can:super-admin');

    Route::prefix('register-approvals')->group(function () {
        Route::get('/', [RegisterApprovalController::class, 'index'])->name('register.approvals.index');
        Route::delete('/{approval}', [RegisterApprovalController::class, 'destroy'])->name('register.approvals.destroy');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/user', [SettingsUserController::class, 'show'])->name('settings.user.show');
        Route::get('/user/{user}', [SettingsUserController::class, 'edit'])->name('settings.user.edit');
        Route::put('/user/{user}', [SettingsUserController::class, 'update'])->name('settings.user.update');
    })->middleware('can:user');
});

Route::get('/email/verify', [VerifyEmailController::class, 'verify'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'handle'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/storage/app/{folder}/{filename}', [ImageController::class, 'find'])->name('user.photo.show');
