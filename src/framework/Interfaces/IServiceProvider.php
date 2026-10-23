<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Interfaces;

interface IServiceProvider
{
    public function boot();

    public function register();
}
