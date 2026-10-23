<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

final class WebRouter
{
    private static array $routes = [];
    private static string $groupPrefix = '';

    public static function get(string $uri, mixed $handler, array $middleware = []): void
    {
        self::add('GET', $uri, $handler, $middleware);
    }

    public static function post(string $uri, mixed $handler, array $middleware = []): void
    {
        self::add('POST', $uri, $handler, $middleware);
    }

    public static function put(string $uri, mixed $handler, array $middleware = []): void
    {
        self::add('PUT', $uri, $handler, $middleware);
    }

    public static function patch(string $uri, mixed $handler, array $middleware = []): void
    {
        self::add('PATCH', $uri, $handler, $middleware);
    }

    public static function delete(string $uri, mixed $handler, array $middleware = []): void
    {
        self::add('DELETE', $uri, $handler, $middleware);
    }

    public static function group(string $prefix, callable $callback): void
    {
        $previousPrefix = self::$groupPrefix;
        self::$groupPrefix = self::normalizePath(self::$groupPrefix . '/' . trim($prefix, '/'));
        try {
            $callback();
        } finally {
            self::$groupPrefix = $previousPrefix;
        }
    }

    public static function match(Request $request): ?array
    {
        $allowedMethods = [];
        foreach (self::$routes as $route) {
            $params = self::matchPath($route['path'], $request->path());
            if ($params === null) {
                continue;
            }
            if ($route['method'] !== $request->method()) {
                $allowedMethods[] = $route['method'];
                continue;
            }

            $route['params'] = $params;
            return $route;
        }

        if ($allowedMethods !== []) {
            return ['method_not_allowed' => true, 'allowed' => array_values(array_unique($allowedMethods))];
        }

        return null;
    }

    public static function routes(): array
    {
        return self::$routes;
    }

    public static function reset(): void
    {
        self::$routes = [];
        self::$groupPrefix = '';
    }

    private static function add(string $method, string $uri, mixed $handler, array $middleware): void
    {
        if (!is_callable($handler) && !is_string($handler) && !is_array($handler) && !is_object($handler)) {
            throw new \InvalidArgumentException('A route handler must be callable or resolvable by the container.');
        }

        $path = self::normalizePath(self::$groupPrefix . '/' . trim($uri, '/'));
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    private static function matchPath(string $routePath, string $requestPath): ?array
    {
        $routeSegments = $routePath === '/' ? [] : explode('/', trim($routePath, '/'));
        $requestSegments = $requestPath === '/' ? [] : explode('/', trim($requestPath, '/'));
        if (count($routeSegments) !== count($requestSegments)) {
            return null;
        }

        $params = [];
        foreach ($routeSegments as $index => $segment) {
            if (preg_match('/^\{([A-Za-z_][A-Za-z0-9_]*)\}$/', $segment, $matches) === 1) {
                $params[$matches[1]] = rawurldecode($requestSegments[$index]);
                continue;
            }
            if ($segment !== $requestSegments[$index]) {
                return null;
            }
        }

        return $params;
    }

    private static function normalizePath(string $path): string
    {
        $segments = array_filter(explode('/', $path), static fn (string $segment): bool => $segment !== '');
        return $segments === [] ? '/' : '/' . implode('/', $segments);
    }
}
