<?php

declare(strict_types=1);

use App\Http\Controllers\LicenseController;
use App\Models\License;
use App\Policies\LicensePolicy;
use App\View\Components\Molecules\SuperuserMenuItems;
use Illuminate\Support\Facades\Route;

Route::get('/', [LicenseController::class, 'index'])
    /**
     * @see SuperuserMenuItems::__construct()
     * @see view('pages.licenses.index')
     */
    ->name('licenses.index')
    ->can('viewAny', License::class);

Route::get('/{license}', [LicenseController::class, 'show'])
    /**
     * @see view('pages.licenses.index')
     */
    ->name('licenses.show')
    ->can('view,license');

Route::patch('/{license}/cancel', [LicenseController::class, 'cancel'])
    /**
     * @see LicensePolicy::cancel()
     * @see view('pages.plans.index')
     */
    ->name('licenses.cancel')
    ->can('cancel,license');

Route::patch('/{license}/activate', [LicenseController::class, 'activate'])
    /**
     * @see LicensePolicy::activate()
     * @see view('pages.plans.index')
     */
    ->name('licenses.activate')
    ->can('activate,license');
