<?php

namespace App\Providers;

use App\Services\Cart\CartFormatter;
use App\Services\Cart\CartFormatterInterface;
use App\Services\Cart\CartManager;
use App\Services\Cart\CartServiceInterface;
use App\Services\Cart\SessionCartService;
use Illuminate\Support\ServiceProvider;
use L5Swagger;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartServiceInterface::class, SessionCartService::class);
        $this->app->bind(CartFormatterInterface::class, CartFormatter::class);

        $this->app->singleton(CartManager::class, function ($app) {
            return new CartManager(
                $app->make(CartServiceInterface::class),
                $app->make(CartFormatterInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
