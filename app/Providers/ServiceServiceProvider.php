<?php

namespace App\Providers;

use App\Services\Contracts\RegistrationServiceInterface;
use App\Services\Registration\RegistrationService;
use Illuminate\Support\ServiceProvider;

final class ServiceServiceProvider extends ServiceProvider
{
    /** @var array{0: string, 1: string}[] */
    protected array $bindables = [
        [
            RegistrationServiceInterface::class,
            RegistrationService::class
        ]
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
        collect($this->bindables)->each(
            fn($arrBind) => $this->app->bind(...$arrBind)
        );
    }
}
