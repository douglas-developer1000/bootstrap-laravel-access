<?php

declare(strict_types=1);

use App\Http\Controllers\PaymentCardController;
use App\Models\PaymentCard;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaymentCardController::class, 'index'])
    /**
     * @see \App\View\Components\Molecules\UserMenuItems::__construct()
     * @see view('pages.payment-cards.index')
     * @see view('pages.payment-cards.create')
     * @see view('pages.payment-cards.edit')
     * @see view('pages.payment-cards.show')
     */
    ->name('payment-cards.index')
    ->can('viewAny', PaymentCard::class);

Route::get('/create', [PaymentCardController::class, 'create'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.create')
    ->can('create', PaymentCard::class);

Route::get('/{card}', [PaymentCardController::class, 'show'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.show')
    ->can('view,card');

Route::get('/{card}/edit', [PaymentCardController::class, 'edit'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.edit')
    ->can('edit,card');

Route::post('/', [PaymentCardController::class, 'store'])
    /**
     * @see view('pages.payment-cards.create')
     */
    ->name('payment-cards.store')
    ->can('store', PaymentCard::class);

Route::put('/{card}', [PaymentCardController::class, 'update'])
    /**
     * @see view('pages.payment-cards.edit')
     */
    ->name('payment-cards.update')
    ->can('update,card');

Route::delete('/group/{key}/{paymentCardList}', [PaymentCardController::class, 'removeGroup'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.group.destroy')
    ->can('deleteList', [PaymentCard::class, 'paymentCardList']);

Route::delete('/{card}', [PaymentCardController::class, 'destroy'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.destroy')
    ->can('delete,card');

Route::post('/group/{key}/{paymentCardList}', [PaymentCardController::class, 'restoreGroup'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.group.restore')
    ->can('restoreList', [PaymentCard::class, 'paymentCardList']);

Route::post('/{payCardDeleted}/restore', [PaymentCardController::class, 'restore'])
    /**
     * @see view('pages.payment-cards.index')
     */
    ->name('payment-cards.restore')
    ->can('restore,payCardDeleted');
