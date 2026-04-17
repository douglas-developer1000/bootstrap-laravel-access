<?php

namespace App\Providers;

use App\Services\Contracts\ImgStoragerInterface;
use App\Services\Contracts\RegistrationInterface;
use App\Services\DropboxImgStoragerService;
use App\Services\DropboxTokenProviderService;
use App\Services\Registration\RegistrationService;
use Illuminate\Support\ServiceProvider;
use Spatie\Dropbox\TokenProvider;
// use App\Services\LocalImgStoragerService;

final class ServiceServiceProvider extends ServiceProvider
{
    public $bindings = [
        RegistrationInterface::class => RegistrationService::class,
        TokenProvider::class => DropboxTokenProviderService::class,
        ImgStoragerInterface::class => DropboxImgStoragerService::class
        // ImgStoragerInterface::class => LocalImgStoragerService::class
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
