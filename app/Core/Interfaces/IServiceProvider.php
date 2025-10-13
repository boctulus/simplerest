<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface IServiceProvider {
    /**
     * Bootstrap the application services.
     * 
     * Inicializa cualquier lógica que requieran los servicios registrados.
     *
     * @return void
     */
    public function boot();

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register();
}