<?php

declare(strict_types=1);

use App\Http\Controllers\ExchangeController;
use App\Models\Exchange;
use App\Models\StockExit;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExchangeController::class, 'index'])
    /**
     * @see App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.stocks.entries.create')
     */
    ->name('exchanges.index')
    ->can('viewExchangeAny', StockExit::class);

Route::delete('/group/{key}/{stockExitList}', [ExchangeController::class, 'removeExchangeGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('exchanges.group.destroy')
    ->can('deleteExchangeList', [StockExit::class, 'stockExitList']);

Route::delete('/{exit}/exits', [ExchangeController::class, 'removeExchange'])
    ->name('exchanges.destroy')
    ->can('deleteExchange,exit');
