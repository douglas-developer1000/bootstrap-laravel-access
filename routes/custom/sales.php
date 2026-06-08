<?php

declare(strict_types=1);

use App\Http\Controllers\SaleController;
use App\Models\Sale;
use Illuminate\Support\Facades\Route;

Route::get('/', [SaleController::class, 'index'])
    /**
     * @see App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.stocks.entries.create')
     */
    ->name('sales.index')
    ->can('viewAny', Sale::class);

Route::get('/{sale}', [SaleController::class, 'show'])
    ->name('sales.show')
    ->can('show,sale');

Route::delete('/group/{key}/{saleList}', [SaleController::class, 'destroyList'])
    /**
     * @see view('pages.sales.index')
     */
    ->name('sales.group.destroy')
    ->can('deleteList', [Sale::class, 'saleList']);

Route::delete('/{sale}', [SaleController::class, 'destroy'])
    ->name('sales.destroy')
    ->can('delete,sale');
