<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Libraries\Enums\PermissionNameEnum;

Route::get('/', [CustomerController::class, 'index'])
    ->name('customers.index')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_INDEX->value);

Route::get('/create', [CustomerController::class, 'create'])
    ->name('customers.create')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_CREATE->value);

Route::get('/{customer}', [CustomerController::class, 'show'])
    ->name('customers.show')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_SHOW->value);

Route::get('/{customer}/edit', [CustomerController::class, 'edit'])
    ->name('customers.edit')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_EDIT->value);

Route::post('/', [CustomerController::class, 'store'])
    ->name('customers.store')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_STORE->value);

Route::put('/{customer}', [CustomerController::class, 'update'])
    ->name('customers.update')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_UPDATE->value);

Route::delete('/{customer}', [CustomerController::class, 'destroy'])
    ->name('customers.destroy')
    ->middleware('can:' . PermissionNameEnum::CUSTOMER_DESTROY->value);
