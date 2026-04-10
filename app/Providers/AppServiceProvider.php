<?php

namespace App\Providers;

use App\View\Components\Atoms\TableHead;
use App\View\Components\Molecules\RootPagination;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('app-pagination', RootPagination::class);
        Blade::component('app-table-head', TableHead::class);
    }
}
