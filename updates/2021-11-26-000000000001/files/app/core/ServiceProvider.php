<?php

namespace simplerest\core;

abstract class ServiceProvider 
{
    /**
     * Bootstrap the application services.
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

