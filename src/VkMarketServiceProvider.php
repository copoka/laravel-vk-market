<?php

declare(strict_types = 1);

namespace Vlsoprun\VkMarket;

use Illuminate\Support\ServiceProvider;

class VkMarketServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('vk-market.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'vk-market');

        $this->app->singleton(Builder::class, function ($app) {
            /** @var \Illuminate\Contracts\Foundation\Application $app */
            return new Builder($app);
        });
    }
}
