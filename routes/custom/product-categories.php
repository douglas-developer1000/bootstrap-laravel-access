<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductCategoryController;
use App\Models\ProductCategory;

Route::get('/', [ProductCategoryController::class, 'index'])
    /**
     * @see \App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.stocks.index')
     */
    ->name('product-categories.index')
    ->can('viewAny', ProductCategory::class);

Route::get('/create', [ProductCategoryController::class, 'create'])
    /**
     * @see view('pages.products.categories.index')
     * @see view('pages.products.create')
     */
    ->name('product-categories.create')
    ->can('create', ProductCategory::class);

Route::get('/{category}', [ProductCategoryController::class, 'show'])
    /**
     * @see view('pages.products.categories.index')
     * @see view('pages.stocks.index')
     */
    ->name('product-categories.show')
    ->can('view,category');

Route::get('/{category}/edit', [ProductCategoryController::class, 'edit'])
    /**
     * @see view('pages.products.categories.index')
     */
    ->name('product-categories.edit')
    ->can('edit,category');

Route::post('/', [ProductCategoryController::class, 'store'])
    /**
     * @see view('pages.products.categories.create')
     */
    ->name('product-categories.store')
    ->can('store', ProductCategory::class);

Route::put('/{category}', [ProductCategoryController::class, 'update'])
    /**
     * @see view('pages.products.categories.edit')
     */
    ->name('product-categories.update')
    ->can('update,category');

Route::delete('/group/{key}/{productCategoryList}', [ProductCategoryController::class, 'removeGroup'])
    /**
     * @see view('pages.products.categories.index')
     */
    ->name('product-categories.group.destroy')
    ->can('deleteList', [ProductCategory::class, 'productCategoryList']);

Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])
    /**
     * @see view('pages.products.categories.index')
     */
    ->name('product-categories.destroy')
    ->can('delete,category');

Route::post('/group/{key}/{productCategoryList}', [ProductCategoryController::class, 'restoreGroup'])
    /**
     * @see view('pages.products.categories.index')
     */
    ->name('product-categories.group.restore')
    ->can('restoreList', [ProductCategory::class, 'productCategoryList']);

Route::post('/{prodCategoryDeleted}/restore', [ProductCategoryController::class, 'restore'])
    /**
     * @see view('pages.products.categories.index')
     */
    ->name('product-categories.restore')
    ->can('restore,prodCategoryDeleted');
