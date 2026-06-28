<?php

declare(strict_types=1);

use App\Http\Controllers\LicenseController;
use App\Models\User;
use App\View\Components\Molecules\SuperuserMenuItems;
use Illuminate\Support\Facades\Route;

Route::get('/', [LicenseController::class, 'index'])
    /**
     * @see SuperuserMenuItems::__construct()
     * @see view('pages.licenses.index')
     */
    ->name('licenses.index')
    ->can('beSuperAdmin', User::class);

Route::get('/{license}', [LicenseController::class, 'show'])
    /**
     * @see view('pages.licenses.index')
     */
    ->name('licenses.show')
    ->can('beSuperAdmin', User::class);

Route::patch('/{license}/cancel', [LicenseController::class, 'cancel'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.cancel')
    ->can('beSuperAdmin', User::class);

Route::patch('/{license}/activate', [LicenseController::class, 'activate'])
    /**
     * @see view('pages.plans.index')
     */
    ->name('plans.activate')
    ->can('beSuperAdmin', User::class);
