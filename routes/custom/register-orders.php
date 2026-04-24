<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterOrderController;
use App\Libraries\Enums\RoleNameEnum;

Route::middleware(['role:' . RoleNameEnum::SUPER_ADMIN->value])->group(function () {
    Route::get('/', [RegisterOrderController::class, 'index'])->name('register.orders.index');
    Route::delete('/{order}', [RegisterOrderController::class, 'destroy'])->name('register.orders.destroy');
    Route::delete('/{order}/approval', [RegisterOrderController::class, 'approve'])->name('register.orders.approve');
});
