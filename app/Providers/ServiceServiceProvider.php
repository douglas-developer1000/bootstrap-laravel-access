<?php

namespace App\Providers;

use App\Services\Contracts\ImgStoragerServiceInterface;
use App\Services\Contracts\RegistrationServiceInterface;
use App\Services\DropboxImgStoragerService;
use App\Services\DropboxTokenProviderService;
use App\Services\Registration\RegistrationService;
use Illuminate\Support\ServiceProvider;
use Spatie\Dropbox\TokenProvider;
// use App\Services\LocalImgStoragerService;

final class ServiceServiceProvider extends ServiceProvider
{
    public $bindings = [
        RegistrationServiceInterface::class => RegistrationService::class,
        TokenProvider::class => DropboxTokenProviderService::class,
        ImgStoragerServiceInterface::class => DropboxImgStoragerService::class
        // ImgStoragerServiceInterface::class => LocalImgStoragerService::class
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
