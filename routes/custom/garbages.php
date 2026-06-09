<?php

declare(strict_types=1);

use App\Http\Controllers\GarbageController;
use App\Models\StockExit;
use Illuminate\Support\Facades\Route;

Route::get('/', [GarbageController::class, 'index'])
    /**
     * @see App\View\Components\Molecules\UserMenuItems::__construct()
     */
    ->name('garbages.index')
    ->can('viewGarbageAny', StockExit::class);

Route::delete('/group/{key}/{stockExitList}', [GarbageController::class, 'destroyGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('garbages.group.destroy')
    ->can('deleteGarbageList', [StockExit::class, 'stockExitList']);

Route::delete('/{exit}', [GarbageController::class, 'destroy'])
    ->name('garbages.destroy')
    ->can('deleteGarbage,exit');
