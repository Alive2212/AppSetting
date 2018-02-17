<?php

namespace Alive2212\AppSetting;

use Illuminate\Support\ServiceProvider;

class AppSettingServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            // load console commands
            $this->commands([
                Console\ClearCommand::class,
            ]);

        }

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/passport'),
            ], 'passport-views');

            $this->publishes([
                __DIR__.'/../resources/assets/js/components' => base_path('resources/assets/js/components/passport'),
            ], 'passport-components');

        }
    }
    /**
     * load migration
     */
    public function registerMigrations()
    {
        // TODO copy migration file when run php artisan vendor:publish
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}