<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterApprovalController;
use App\Models\User;
use Illuminate\Support\Str;

Route::middleware([Str::of('can:beSuperAdmin,')->append(User::class)->toString()])->group(function () {
    Route::get('/', [RegisterApprovalController::class, 'index'])->name('register.approvals.index');
    Route::delete('/group', [RegisterApprovalController::class, 'removeGroup'])->name('register.approvals.group.destroy');
    Route::delete('/{approval}', [RegisterApprovalController::class, 'destroy'])->name('register.approvals.destroy');
});
