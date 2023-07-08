<?php

namespace App\Providers;

use App\Interfaces\ApiMedicAuthServiceInterface;
use App\Interfaces\ApiMedicHealthServiceInterface;
use App\Interfaces\AuthServiceInterface;
use App\Services\ApiMedicAuthService;
use App\Services\ApiMedicHealthService;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(ApiMedicAuthServiceInterface::class, ApiMedicAuthService::class);
        $this->app->singleton(ApiMedicHealthServiceInterface::class, ApiMedicHealthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
