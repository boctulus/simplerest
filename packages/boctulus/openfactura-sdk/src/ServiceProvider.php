<?php

namespace Boctulus\OpenfacturaSdk;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * The parent boot() method automatically loads:
     * - Package configuration from config/config.php
     * - Web routes from config/routes.php
     * - CLI routes from config/cli_routes.php
     *
     * Override this method only if you need additional bootstrapping logic.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Add your custom bootstrap logic here if needed
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register package services here
    }
}

