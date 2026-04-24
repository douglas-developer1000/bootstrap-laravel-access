<?php

namespace App\Providers;

use App\View\Components\Atoms\TableHead;
use App\View\Components\Molecules\RootPagination;
use App\View\Components\Molecules\SuperuserMenuItem;
use App\View\Components\Molecules\UserMenuItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeComponentProvider extends ServiceProvider
{
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
        Blade::component('app-pagination', RootPagination::class);
        Blade::component('app-table-head', TableHead::class);
        Blade::component('superuser-menu-items', SuperuserMenuItem::class);
        Blade::component('user-menu-items', UserMenuItem::class);
    }
}
