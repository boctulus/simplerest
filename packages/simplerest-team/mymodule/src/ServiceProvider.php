<?php

namespace SimplerestTeam\Mymodule;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

/**
 * Service Provider for MyModule
 *
 * Este archivo fue generado automáticamente durante la conversión de módulo a package.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar servicios, cargar configuración, etc.

        // Cargar rutas si existen
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            require_once $routesFile;
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Código que se ejecuta al inicio de la aplicación

        // Cargar vistas, publicar assets, etc.
    }
}
