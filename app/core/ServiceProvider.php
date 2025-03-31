<?php

namespace Boctulus\Simplerest\Core;

abstract class ServiceProvider 
{
    /**
     * Bootstrap the application services.
     * 
     * Inicializa cualquier lógica que requieran los servicios registrados.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Register the application services.
     *
     * @return void
     */
    abstract public function register();
}

