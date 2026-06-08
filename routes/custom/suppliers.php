<?php

declare(strict_types=1);

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Models\Supplier;

Route::get('/', [SupplierController::class, 'index'])
    /**
     * @see \App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.stocks.index')
     */
    ->name('suppliers.index')
    ->can('viewAny', Supplier::class);

Route::get('/create', [SupplierController::class, 'create'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('suppliers.create')
    ->can('create', Supplier::class);

Route::get('/{supplier}', [SupplierController::class, 'show'])
    ->name('suppliers.show')
    ->can('view,supplier');

Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])
    ->name('suppliers.edit')
    ->can('edit,supplier');

Route::post('/', [SupplierController::class, 'store'])
    /**
     * @see view('pages.suppliers.create')
     */
    ->name('suppliers.store')
    ->can('store', Supplier::class);

Route::put('/{supplier}/edit', [SupplierController::class, 'update'])
    /**
     * @see view('pages.suppliers.edit')
     */
    ->name('suppliers.update')
    ->can('update,supplier');

Route::delete('/group/{key}/{supplierList}', [SupplierController::class, 'removeGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('suppliers.group.destroy')
    ->can('deleteList', [Supplier::class, 'supplierList']);

Route::delete('/{supplier}', [SupplierController::class, 'destroy'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('suppliers.destroy')
    ->can('delete,supplier');

Route::post('/group/{key}/{supplierList}', [SupplierController::class, 'restoreGroup'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('suppliers.group.restore')
    ->can('restoreList', [Supplier::class, 'supplierList']);

Route::post('/{supplierDeleted}/restore', [SupplierController::class, 'restore'])
    /**
     * @see view('pages.suppliers.index')
     */
    ->name('suppliers.restore')
    ->can('restore,supplierDeleted');
