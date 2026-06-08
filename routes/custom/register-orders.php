<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterOrderController;
use App\Models\User;
use Illuminate\Support\Str;

Route::middleware([Str::of('can:beSuperAdmin,')->append(User::class)->toString()])->group(function () {
    Route::get('/', [RegisterOrderController::class, 'index'])->name('register.orders.index');
    Route::delete('/group', [RegisterOrderController::class, 'removeGroup'])->name('register.orders.group.destroy');
    Route::delete('/group/approval', [RegisterOrderController::class, 'approveGroup'])->name('register.orders.group.approve');
    Route::delete('/{order}', [RegisterOrderController::class, 'destroy'])->name('register.orders.destroy');
    Route::delete('/{order}/approval', [RegisterOrderController::class, 'approve'])->name('register.orders.approve');
});
