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
    ->can('viewAny', Exchange::class);

Route::delete('/group/{key}/{exchangeList}', [ExchangeController::class, 'destroyGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('exchanges.group.destroy')
    ->can('deleteList', [Exchange::class, 'exchangeList']);

Route::delete('/{exchange}/{exit}', [ExchangeController::class, 'destroy'])
    ->name('exchanges.destroy')
    ->can('delete', [Exchange::class, 'exchange', 'exit']);
