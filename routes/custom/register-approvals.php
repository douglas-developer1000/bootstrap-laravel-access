<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterApprovalController;

Route::get('/', [RegisterApprovalController::class, 'index'])->name('register.approvals.index');
Route::delete('/group', [RegisterApprovalController::class, 'removeGroup'])->name('register.approvals.group.destroy');
Route::delete('/{approval}', [RegisterApprovalController::class, 'destroy'])->name('register.approvals.destroy');
