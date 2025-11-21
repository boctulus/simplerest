<?php

namespace Boctulus\Simplerest\Modules\xeni;

use Boctulus\Simplerest\Core\ServiceProvider;

class ModuleProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $routesPath = __DIR__ . '/../config/routes.php';
        if (file_exists($routesPath)) {
            include $routesPath;
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