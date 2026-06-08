<?php

declare(strict_types=1);

use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;
use App\Models\Discount;
use App\Models\User;

Route::get('/', [DiscountController::class, 'index'])
    /**
     * @see \App\View\Components\Molecules\UserMenuItems::__construct()
     */
    ->name('discounts.index')
    ->can('viewAny', Discount::class);

Route::get('/create', [DiscountController::class, 'create'])
    /**
     * @see view('pages.discounts.index')
     */
    ->name('discounts.create')
    ->can('create', Discount::class);

Route::get('/{discount}/edit', [DiscountController::class, 'edit'])
    /**
     * @see view('pages.discounts.index')
     */
    ->name('discounts.edit')
    ->can('edit,discount');

Route::post('/', [DiscountController::class, 'store'])
    /**
     * @see view('pages.discounts.create')
     */
    ->name('discounts.store')
    ->can('store', Discount::class);

Route::put('/{discount}', [DiscountController::class, 'update'])
    /**
     * @see view('pages.discounts.edit')
     */
    ->name('discounts.update')
    // ->can('update,discount')
;

Route::delete('/group/{key}/{discountList}', [DiscountController::class, 'removeGroup'])
    /**
     * @see view('pages.discounts.index')
     */
    ->name('discounts.group.destroy')
    ->can('deleteList', [Discount::class, 'discountList']);

Route::delete('/{discount}', [DiscountController::class, 'destroy'])
    /**
     * @see view('pages.discounts.index')
     */
    ->name('discounts.destroy')
    ->can('delete,discount');

Route::post('/group/{key}/{discountList}', [DiscountController::class, 'restoreGroup'])
    /**
     * @see view('pages.discounts.index')
     */
    ->name('discounts.group.restore')
    ->can('restoreList', [Discount::class, 'discountList']);

Route::post('/{discountDeleted}/restore', [DiscountController::class, 'restore'])
    /**
     * @see view('pages.discounts.index')
     */
    ->name('discounts.restore')
    ->can('restore,discountDeleted');

Route::post('/flush', [DiscountController::class, 'flushPersistence'])
    ->name('discounts.flush')
    ->can('beSuperAdmin', User::class);
