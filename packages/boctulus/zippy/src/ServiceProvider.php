<?php

namespace Boctulus\Zippy;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;
use Boctulus\Simplerest\Core\Libs\Config;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load package configuration
        $configFile = __DIR__ . '/../config/config.php';
        if (file_exists($configFile)) {
            $packageConfig = include $configFile;

            // Extract vendor and package from namespace
            // Expected format: Vendor\Package
            $namespace = __NAMESPACE__;
            $parts = explode('\\', $namespace);

            if (count($parts) >= 2) {
                $vendor = strtolower($parts[0]);
                $package = strtolower($parts[1]);

                Config::loadPackageConfig($vendor, $package, $packageConfig);
            }
        }

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

