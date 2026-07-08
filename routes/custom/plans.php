<?php

declare(strict_types=1);

use App\Http\Controllers\PlanController;
use App\Http\Controllers\SettingsPlanController;
use App\Models\User;
use App\View\Components\Molecules\SuperuserMenuItems;
use Illuminate\Support\Facades\Route;

Route::get('/', [PlanController::class, 'index'])
    /**
     * @see SuperuserMenuItems::__construct()
     * @see view('pages.plans.index')
     */
    ->name('plans.index')
    ->can('beSuperAdmin', User::class);

Route::get('view', [SettingsPlanController::class, 'index'])
    ->name('plans.view.index');

Route::get('view/{plan}', [SettingsPlanController::class, 'show'])
    ->name('plans.view.show');

Route::get('/create', [PlanController::class, 'create'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.create')
    ->can('beSuperAdmin', User::class);

Route::get('/{plan:slug}', [PlanController::class, 'show'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.show')
    ->can('beSuperAdmin', User::class);

Route::get('/{plan:slug}/edit', [PlanController::class, 'edit'])
    /**
     * @see view('pages.plans.index')
     * @see view('pages.plans.show')
     */
    ->name('plans.edit')
    ->can('beSuperAdmin', User::class);

Route::post('/', [PlanController::class, 'store'])
    /**
     * @see view('pages.plans.create')
     */
    ->name('plans.store')
    ->can('beSuperAdmin', User::class);

Route::put('/{plan:slug}', [PlanController::class, 'update'])
    /**
     * @see view('pages.plans.edit')
     */
    ->name('plans.update')
    ->can('beSuperAdmin', User::class);

Route::delete('/group/{key}/{planList}', [PlanController::class, 'removeGroup'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.group.destroy')
    ->can('beSuperAdmin', User::class);

Route::delete('/{plan:slug}', [PlanController::class, 'destroy'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.destroy')
    ->can('beSuperAdmin', User::class);

Route::post('/flush', [PlanController::class, 'flush'])
    ->name('plans.flush')
    ->can('beSuperAdmin', User::class);

Route::post('/group/{key}/{planList}', [PlanController::class, 'restoreGroup'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.group.restore')
    ->can('beSuperAdmin', User::class);

Route::post('/{planDeleted}/restore', [PlanController::class, 'restore'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.restore')
    ->can('beSuperAdmin', User::class);
