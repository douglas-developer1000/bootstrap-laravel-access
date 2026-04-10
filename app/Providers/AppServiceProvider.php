<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::before(function ($user, $ability) {
            // superadmin email by APP_SUPERADMIN_EMAIL env
            if ($user->email === config('app.superadmin.email')) {
                return true;
            }
            if ($user->hasRole('super-admin')) {
                return true;
            }
            return null;
        });
    }
}
