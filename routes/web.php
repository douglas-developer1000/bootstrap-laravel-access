<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

$customDir = __DIR__ . DIRECTORY_SEPARATOR . 'custom';

collect(['guest.php', 'verify-email.php'])->each(function (string $filename) use ($customDir) {
    include_once $customDir . DIRECTORY_SEPARATOR . $filename;
});

Route::post('/signout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::middleware(['auth', 'verified'])->group(function () use ($customDir) {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    collect([
        'permissions.php',
        'roles.php',
        'users.php',
        'register-orders.php',
        'register-approvals.php',
        'settings.php',
        'customers.php'
    ])->each(function (string $filename) use ($customDir) {
        include_once $customDir . DIRECTORY_SEPARATOR . $filename;
    });
});

Route::get('/storage/app/{folder}/{filename}', [ImageController::class, 'find'])->name('user.photo.show');
