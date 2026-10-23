<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

abstract class Middleware
{
    abstract public function handle(Request $request, callable $next): mixed;
}
