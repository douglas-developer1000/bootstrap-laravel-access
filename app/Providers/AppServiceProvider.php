<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

final class AppServiceProvider extends ServiceProvider
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
        Gate::before(function ($user, $ability, $arrArgs) {
            if (
                // superadmin email by APP_SUPERADMIN_EMAIL env
                $user->email === config('app.superadmin.email') ||
                $user->hasRole('super-admin')
            ) {
                switch ($ability) {
                    case 'remove-user':
                        [$toRemove] = $arrArgs;
                        return $user->id !== $toRemove->id;
                    default:
                        return true;
                }
            }
            return null;
        });
    }
}
