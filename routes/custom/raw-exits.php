<?php

declare(strict_types=1);

use App\Http\Controllers\RawExitController;
use App\Models\StockExit;
use Illuminate\Support\Facades\Route;

Route::get('/', [RawExitController::class, 'index'])
    /**
     * @see App\View\Components\Molecules\UserMenuItems::__construct()
     */
    ->name('raw.exits.index')
    ->can('viewRawExitAny', StockExit::class);

Route::delete('/group/{key}/{stockExitList}', [RawExitController::class, 'destroyGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('raw.exits.group.destroy')
    ->can('deleteList', [StockExit::class, 'stockExitList']);

Route::delete('/{exit}', [RawExitController::class, 'destroy'])
    ->name('raw.exits.destroy')
    ->can('delete,exit');
