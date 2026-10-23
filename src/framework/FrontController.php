<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Handlers\ErrorHandler;
use Boctulus\Simplerest\Core\Handlers\MiddlewareHandler;
use Throwable;

final class FrontController
{
    public static function handle(?Request $request = null): Response
    {
        try {
            $request ??= Request::fromGlobals();
            $route = WebRouter::match($request);

            if ($route === null) {
                return Response::json(['error' => 'Not Found'], 404);
            }
            if (($route['method_not_allowed'] ?? false) === true) {
                return Response::json(['error' => 'Method Not Allowed'], 405, [
                    'Allow' => implode(', ', $route['allowed']),
                ]);
            }

            $request->setAttribute('route.params', $route['params']);
            $destination = static fn (Request $currentRequest): mixed => self::invokeHandler(
                $route['handler'],
                $currentRequest,
                $route['params']
            );
            $result = (new MiddlewareHandler())->run($request, $route['middleware'], $destination);

            return $result instanceof Response ? $result : Response::json($result);
        } catch (Throwable $error) {
            return (new ErrorHandler())->handle($error);
        }
    }

    public static function resolve(): void
    {
        self::handle()->send();
    }

    private static function invokeHandler(mixed $handler, Request $request, array $params): mixed
    {
        if (is_callable($handler)) {
            return $handler($request, $params);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$target, $method] = $handler;
            $instance = is_string($target) ? Container::make($target) : $target;
            if (is_object($instance) && is_string($method) && is_callable([$instance, $method])) {
                return $instance->{$method}($request, $params);
            }
        }

        if (is_string($handler)) {
            [$class, $method] = str_contains($handler, '@')
                ? explode('@', $handler, 2)
                : [$handler, '__invoke'];
            $instance = Container::make($class);
            if (is_callable([$instance, $method])) {
                return $instance->{$method}($request, $params);
            }
        }

        if (is_object($handler) && is_callable($handler)) {
            return $handler($request, $params);
        }

        throw new \InvalidArgumentException('The route handler is not callable.');
    }
}
