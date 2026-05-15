<?php

namespace Boctulus\Simplerest\Modules\Security;

use Boctulus\Simplerest\Core\ServiceProvider;

class ModuleProvider extends ServiceProvider
{
    public function boot()
    {
        $routesPath = __DIR__ . '/../config/routes.php';
        if (file_exists($routesPath)) {
            include $routesPath;
        }
    }

    public function register()
    {
    }
}
