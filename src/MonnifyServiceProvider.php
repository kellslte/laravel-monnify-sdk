<?php

namespace Scwar\Monnify;

use Illuminate\Support\ServiceProvider;
use Scwar\Monnify\Services\AuthService;
use Scwar\Monnify\Services\InvoiceService;
use Scwar\Monnify\Services\MonnifyService;
use Scwar\Monnify\Services\SubaccountService;
use Scwar\Monnify\Services\TransactionService;

class MonnifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/monnify.php',
            'monnify'
        );

        // Register AuthService as singleton
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                config('monnify.api_key'),
                config('monnify.secret_key'),
                config('monnify.base_url'),
                config('monnify.cache')
            );
        });

        // Register TransactionService
        $this->app->singleton(TransactionService::class, function ($app) {
            return new TransactionService(
                $app->make(AuthService::class),
                config('monnify.contract_code'),
                config('monnify.base_url')
            );
        });

        // Register InvoiceService
        $this->app->singleton(InvoiceService::class, function ($app) {
            return new InvoiceService(
                $app->make(AuthService::class),
                config('monnify.contract_code'),
                config('monnify.base_url')
            );
        });

        // Register SubaccountService
        $this->app->singleton(SubaccountService::class, function ($app) {
            return new SubaccountService(
                $app->make(AuthService::class),
                config('monnify.base_url')
            );
        });

        // Register main MonnifyService
        $this->app->singleton(MonnifyService::class, function ($app) {
            return new MonnifyService(
                $app->make(AuthService::class),
                $app->make(TransactionService::class),
                $app->make(InvoiceService::class),
                $app->make(SubaccountService::class)
            );
        });

        // Register facade binding
        $this->app->alias(MonnifyService::class, 'monnify');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/monnify.php' => config_path('monnify.php'),
        ], 'monnify-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'monnify-migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
