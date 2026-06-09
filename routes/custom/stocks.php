<?php

declare(strict_types=1);

use App\Http\Controllers\StockController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockExitController;
use App\Libraries\Enums\StockExitTypeEnum;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockExit;

Route::get('/', [StockController::class, 'index'])
    /**
     * @see App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.stocks.entries.create')
     */
    ->name('stocks.index')
    ->can('viewAny', Product::class);

Route::get('/exits/{exitType}/create', [StockExitController::class, 'createExit'])
    /**
     * @ see view('pages.stocks.show')
     */
    ->name('stocks.exits.create')
    ->where([
        'exitType' => implode('|', [
            StockExitTypeEnum::PERSONAL_USE->value,
            StockExitTypeEnum::DEMONSTRATION->value,
            StockExitTypeEnum::EXCHANGE->value,
            StockExitTypeEnum::LOSS->value,
        ])
    ])
    ->can('createExit', [StockExit::class, 'exitType']);

Route::get('/exits/{exitType}/{customer}/create', [StockExitController::class, 'createSaleExit'])
    /**
     * @ see view('pages.stocks.show')
     */
    ->name('stocks.exits.sale.create')
    ->can('createSaleExit', [StockExit::class, 'exitType', 'customer'])
    ->where([
        'exitType' => StockExitTypeEnum::SALE->value
    ]);

Route::post('/exits/{exitType}', [StockExitController::class, 'storeExit'])
    /**
     * @see view('pages.stocks.exits.shared.sale')
     * @see view('pages.stocks.exits.shared.remain')
     * @see view('pages.stocks.exits.shared.exchange')
     */
    ->name('stocks.exits.store')
    ->where([
        'exitType' => implode('|', [
            StockExitTypeEnum::PERSONAL_USE->value,
            StockExitTypeEnum::DEMONSTRATION->value,
            StockExitTypeEnum::EXCHANGE->value,
            StockExitTypeEnum::LOSS->value,
        ])
    ])
    ->can('store', [StockExit::class, 'exitType']);

Route::get('/{product}', [StockController::class, 'show'])
    /**
     * @see view('pages.stocks.entries.create')
     * @see view('pages.stocks.index')
     */
    ->name('stocks.show')
    ->can('show,product');

Route::get('/{productDeleted}/removed', [StockController::class, 'showDeleted'])
    /**
     * @see view('pages.stocks.entries.create')
     * @see view('pages.stocks.index')
     */
    ->name('stocks.show.removed')
    ->can('show,productDeleted');

Route::post('/mark/{product}/sales', [StockExitController::class, 'markSale'])
    ->name('stocks.sales.mark')
    ->can('mark', [StockExit::class, 'product']);

Route::post('/unmark/{product}/sales', [StockExitController::class, 'unmarkSale'])
    ->name('stocks.sales.unmark')
    ->can('unmark', [StockExit::class, 'product']);

Route::get('/{product}/entries/create', [StockEntryController::class, 'createEntry'])
    /**
     * @see view('pages.stocks.show')
     * @see view('pages.stocks.index')
     */
    ->name('stocks.entries.create')
    ->can('create', [StockEntry::class, 'product']);

Route::post('/{product}/entries', [StockEntryController::class, 'storeEntry'])
    /**
     * @see view('pages.stocks.entries.create')
     */
    ->name('stocks.entries.store')
    ->can('store', [StockEntry::class, 'product']);
