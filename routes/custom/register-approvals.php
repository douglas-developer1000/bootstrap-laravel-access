<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterApprovalController;

Route::prefix('register-approvals')->group(function () {
    Route::get('/', [RegisterApprovalController::class, 'index'])->name('register.approvals.index');
    Route::delete('/{approval}', [RegisterApprovalController::class, 'destroy'])->name('register.approvals.destroy');
});
