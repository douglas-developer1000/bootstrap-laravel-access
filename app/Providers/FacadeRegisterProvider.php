<?php

declare(strict_types=1);

namespace App\Providers;

use App\Libraries\Helpers\CheckListHelper;
use App\Libraries\Helpers\ListStoragerHelper;
use App\Libraries\Helpers\PaginatorHelper;
use App\Libraries\Helpers\TimingProtectionHelper;
use Illuminate\Support\ServiceProvider;

final class FacadeRegisterProvider extends ServiceProvider
{
    public $singletons = [
        'TimingProtection' => TimingProtectionHelper::class,
        'CheckList' => CheckListHelper::class,
        'ListStorager' => ListStoragerHelper::class,
        'Paginator' => PaginatorHelper::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
