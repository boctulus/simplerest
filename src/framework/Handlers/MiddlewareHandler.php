<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Container;
use Boctulus\Simplerest\Core\Exceptions\MiddlewareNotFoundException;
use Boctulus\Simplerest\Core\Middleware;
use Boctulus\Simplerest\Core\Request;
use Throwable;

final class MiddlewareHandler
{
    public function run(Request $request, array $middleware, callable $destination): mixed
    {
        $pipeline = $destination;
        foreach (array_reverse($middleware) as $definition) {
            $instance = $this->resolve($definition);
            $next = $pipeline;
            $pipeline = static function (Request $currentRequest) use ($instance, $next): mixed {
                if ($instance instanceof Middleware) {
                    return $instance->handle($currentRequest, $next);
                }
                if (is_callable($instance)) {
                    return $instance($currentRequest, $next);
                }
                if (is_object($instance) && is_callable([$instance, 'handle'])) {
                    return $instance->handle($currentRequest, $next);
                }

                throw new MiddlewareNotFoundException('A configured middleware does not provide a request handler.');
            };
        }

        return $pipeline($request);
    }

    private function resolve(mixed $definition): mixed
    {
        if (is_object($definition) || is_callable($definition)) {
            return $definition;
        }
        if (!is_string($definition)) {
            throw new MiddlewareNotFoundException('A configured middleware could not be resolved.');
        }

        try {
            return Container::make($definition);
        } catch (Throwable $exception) {
            throw new MiddlewareNotFoundException('A required middleware provider is unavailable.', 0, $exception);
        }
    }
}
