<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

final class WebRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {
            collect([
                'guest.php',
                'verify-email.php',
                'default.php'
            ])->each(function ($filename) {
                Route::middleware(['web'])->group(
                    base_path(
                        implode(DIRECTORY_SEPARATOR, [
                            'routes',
                            'custom',
                            $filename
                        ])
                    )
                );
            });
            collect([
                ['customers', 'customers.php'],
                ['settings', 'settings.php'],
                ['register-approvals', 'register-approvals.php'],
                ['register-orders', 'register-orders.php'],
                ['users', 'users.php'],
                ['roles', 'roles.php'],
                ['permissions', 'permissions.php'],
                ['stocks', 'stocks.php'],
                ['product-category', 'product-categories.php'],
                ['products', 'products.php'],
                ['suppliers', 'suppliers.php'],
                ['discounts', 'discounts.php'],
                ['payment-cards', 'payment-cards.php'],
                ['sales', 'sales.php'],
                ['exchanges', 'exchanges.php'],
                ['losses', 'losses.php'],
            ])->each(function (array $item) {
                [$prefix, $filename] = $item;

                Route::middleware(['web', 'auth', 'verified', 'throttle:60,1'])
                    ->prefix($prefix)
                    ->group(
                        base_path(
                            implode(DIRECTORY_SEPARATOR, [
                                'routes',
                                'custom',
                                $filename
                            ])
                        )
                    );
            });
        });
    }
}
