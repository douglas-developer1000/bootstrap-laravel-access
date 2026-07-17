<?php

declare(strict_types=1);

namespace App\Providers;

use App\Libraries\Enums\RoleNameEnum;
use App\Models\License;
use App\Models\User;
use App\Observers\LicenseObserver;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->runSuperAdminAuthorization();

        License::observe(LicenseObserver::class);

        $this->declareViewCallables();
    }

    protected function declareViewCallables(): void
    {
        View::composer('*', function ($view) {
            $view->with('parsePrice', fn (BigDecimal|float|int $price): string => (
                Number::currency(
                    number: BigDecimal::of(
                        \is_float($price) ? "{$price}" : $price
                    )->toFloat(),
                    in: 'BRL',
                    locale: 'pt_BR',
                    precision: 2
                )
            ));
        });
    }

    /**
     * NOTE: The value of config('app.superadmin.email'):
     * - refers to superadmin email
     * - is defined by APP_SUPERADMIN_EMAIL env.
     */
    protected function runSuperAdminAuthorization(): void
    {
        Gate::before(function (User $user, $ability, $arrArgs) {
            if (
                $user->email === config('app.superadmin.email') ||
                $user->hasRole(RoleNameEnum::SUPER_ADMIN->value)
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
