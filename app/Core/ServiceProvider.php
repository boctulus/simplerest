<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Interfaces\IServiceProvider;

abstract class ServiceProvider implements IServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Get the directory of the child class (the actual package ServiceProvider)
        $reflection = new \ReflectionClass($this);
        $packageDir = dirname($reflection->getFileName());

        // Load package configuration
        $configFile = $packageDir . '/../config/config.php';
        if (file_exists($configFile)) {
            $packageConfig = include $configFile;

            // Extract vendor and package from namespace of the child class
            // Expected format: Vendor\Package
            $namespace = $reflection->getNamespaceName();
            $parts = explode('\\', $namespace);

            if (count($parts) >= 2) {
                $vendor = strtolower($parts[0]);
                $package = strtolower($parts[1]);

                Config::loadPackageConfig($vendor, $package, $packageConfig);
            }
        }

        // Load package web routes
        $routesFile = $packageDir . '/../config/routes.php';
        if (file_exists($routesFile)) {
            include $routesFile;
        }

        // Load package CLI routes
        $cliRoutesFile = $packageDir . '/../config/cli_routes.php';
        if (file_exists($cliRoutesFile)) {
            include $cliRoutesFile;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    abstract public function register();
}

