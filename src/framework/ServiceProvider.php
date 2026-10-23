<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Interfaces\IServiceProvider;

abstract class ServiceProvider implements IServiceProvider
{
    public function boot()
    {
    }

    abstract public function register();
}
