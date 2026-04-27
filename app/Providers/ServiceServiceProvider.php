<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Contracts\ImgStoragerInterface;
use App\Services\Contracts\RegistrationInterface;
use App\Services\Registration\RegistrationService;
use Illuminate\Support\ServiceProvider;
use Spatie\Dropbox\TokenProvider;
use App\Services\Contracts\ImgConverterInterface;
use App\Services\ImgHandling\InterventionImgConverterService;
use App\Services\ImgHandling\DropboxTokenProviderService;
use App\Services\ImgHandling\LocalImgStoragerService;
use App\Services\ImgHandling\DropboxImgStoragerService;

final class ServiceServiceProvider extends ServiceProvider
{
    public $bindings = [
        RegistrationInterface::class => RegistrationService::class,
        TokenProvider::class => DropboxTokenProviderService::class,
        ImgConverterInterface::class => InterventionImgConverterService::class
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $env = config('app.env');
        switch ($env) {
            case 'development':
            case 'local':
                $this->app->bind(ImgStoragerInterface::class, LocalImgStoragerService::class);
                break;
            default:
                $this->app->bind(ImgStoragerInterface::class, DropboxImgStoragerService::class);
                break;
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 
    }
}
