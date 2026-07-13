<?php

declare(strict_types=1);

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/create', [ProductController::class, 'create'])
    /**
     * @see view('pages.stocks.index')
     */
    ->name('products.create')
    ->can('create', Product::class);

Route::get('/{product}/edit', [ProductController::class, 'edit'])
    ->name('products.edit')
    ->can('edit,product');

Route::post('/', [ProductController::class, 'store'])
    /**
     * @see view('pages.products.create')
     */
    ->name('products.store')
    ->can('store', Product::class);

Route::put('/{product}', [ProductController::class, 'update'])
    /**
     * @see view('pages.products.create')
     */
    ->name('products.update')
    ->can('update,product');

Route::delete('/group/{key}/{productList}', [ProductController::class, 'removeGroup'])
    /**
     * @see view('pages.stocks.index')
     */
    ->name('products.group.destroy')
    ->can('deleteList', [Product::class, 'productList']);

Route::delete('/{product}', [ProductController::class, 'destroy'])
    /**
     * @see view('pages.stocks.index')
     */
    ->name('products.destroy')
    ->can('delete,product');

Route::post('/group/{key}/{productList}', [ProductController::class, 'restoreGroup'])
    /**
     * @see view('pages.stocks.index')
     */
    ->name('products.group.restore')
    ->can('restoreList', [Product::class, 'productList']);

Route::post('/{productDeleted}/restore', [ProductController::class, 'restore'])
    /**
     * @see view('pages.stocks.index')
     */
    ->name('products.restore')
    ->can('restore,productDeleted');
