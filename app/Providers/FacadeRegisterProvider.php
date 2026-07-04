<?php

namespace App\Providers;

use App\Libraries\Helpers\TimeProtection;
use Illuminate\Support\ServiceProvider;

class FacadeRegisterProvider extends ServiceProvider
{
    public $singletons = [
        'TimingProtection' => TimeProtection::class
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
