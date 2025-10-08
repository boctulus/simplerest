<?php

namespace Boctulus\CliTest;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load package web routes
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            include $routesFile;
        }

        // Load package CLI routes
        $cliRoutesFile = __DIR__ . '/../config/cli_routes.php';
        if (file_exists($cliRoutesFile)) {
            include $cliRoutesFile;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}

