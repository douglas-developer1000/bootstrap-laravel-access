<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Models\Customer;

Route::get('/', [CustomerController::class, 'index'])
    /**
     * @see \App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.customers.index')
     */
    ->name('customers.index')
    ->can('viewAny', Customer::class);

Route::get('/create', [CustomerController::class, 'create'])
    ->name('customers.create')
    ->can('create', Customer::class);

Route::get('/{customer}', [CustomerController::class, 'show'])
    ->name('customers.show')
    ->can('view,customer');

Route::get('/{customer}/edit', [CustomerController::class, 'edit'])
    ->name('customers.edit')
    ->can('edit,customer');

Route::post('/', [CustomerController::class, 'store'])
    ->name('customers.store')
    ->can('store', Customer::class);

Route::put('/{customer}', [CustomerController::class, 'update'])
    ->name('customers.update')
    ->can('update,customer');

Route::delete('/group/{key}/{customerList}', [CustomerController::class, 'removeGroup'])
    /**
     * @see view('pages.customers.index')
     */
    ->name('customers.group.destroy')
    ->can('deleteList', [Customer::class, 'customerList']);

Route::delete('/{customer}', [CustomerController::class, 'destroy'])
    ->name('customers.destroy')
    ->can('delete,customer');

Route::post('/group/{key}/{customerList}', [CustomerController::class, 'restoreGroup'])
    /**
     * @see view('pages.customers.index')
     */
    ->name('customers.group.restore')
    ->can('restoreList', [Customer::class, 'customerList']);

Route::post('/{customerDeleted}/restore', [CustomerController::class, 'restore'])
    /**
     * @see view('pages.customers.index')
     */
    ->name('customers.restore')
    ->can('restore,customerDeleted');
