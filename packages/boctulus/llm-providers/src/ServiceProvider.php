<?php

namespace Boctulus\LLMProviders;

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

/**
 * Service Provider para el package LLM Providers
 *
 * Este provider se encarga de registrar y arrancar el package
 * dentro del framework Simplerest.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Cargar archivo de configuración si existe
        $configFile = __DIR__ . '/../config/config.php';
        if (file_exists($configFile)) {
            // Aquí podrías cargar configuraciones personalizadas
            // Por ejemplo, registrar providers adicionales, etc.
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
        // Aquí podrías registrar servicios en el contenedor
        // Por ejemplo, registrar el LLMFactory como singleton, etc.
    }
}
