<?php

declare(strict_types=1);

use App\Http\Controllers\LossController;
use App\Models\StockExit;
use Illuminate\Support\Facades\Route;

Route::get('/', [LossController::class, 'index'])
    /**
     * @see App\View\Components\Molecules\UserMenuItems::__construct()
     */
    ->name('losses.index')
    ->can('viewLossAny', StockExit::class);

Route::delete('/group/{key}/{stockExitList}', [LossController::class, 'removeLossGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('losses.group.destroy')
    ->can('deleteLossList', [StockExit::class, 'stockExitList']);

Route::delete('/{exit}/exits', [LossController::class, 'removeLoss'])
    ->name('losses.destroy')
    ->can('deleteLoss,exit');
