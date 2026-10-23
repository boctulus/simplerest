<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;

final class Container
{
    private static array $bindings = [];
    private static array $instances = [];
    private static array $contracts = [];

    public static function bind(string $key, mixed $resolver, bool $singleton = false): void
    {
        if (!is_string($resolver) && !is_object($resolver)) {
            throw new InvalidArgumentException('A service binding must be a class name, object, or closure.');
        }

        self::$bindings[$key] = ['resolver' => $resolver, 'singleton' => $singleton];
        unset(self::$instances[$key]);
    }

    public static function singleton(string $key, mixed $resolver): void
    {
        self::bind($key, $resolver, true);
    }

    public static function useContract(string $class, string $interface, string $implementationClass): void
    {
        self::$contracts[$class][$interface] = $implementationClass;
    }

    public static function getImplementation(string $interface): ?string
    {
        foreach (self::$contracts as $implementations) {
            if (isset($implementations[$interface])) {
                return $implementations[$interface];
            }
        }

        return null;
    }

    public static function has(string $key): bool
    {
        return isset(self::$bindings[$key]) || isset(self::$instances[$key]) || class_exists($key);
    }

    public static function make(string $key, array $params = []): mixed
    {
        $serviceId = $key;
        if (interface_exists($key)) {
            $serviceId = self::getImplementation($key)
                ?? throw new InvalidArgumentException("No service is bound for contract {$key}.");
        }

        if (array_key_exists($serviceId, self::$instances)) {
            return self::$instances[$serviceId];
        }

        $binding = self::$bindings[$serviceId] ?? self::$bindings[$key] ?? null;
        if ($binding === null) {
            return self::construct($serviceId, $params);
        }

        $resolver = $binding['resolver'];
        if ($resolver instanceof Closure) {
            $instance = $resolver();
        } elseif (is_object($resolver)) {
            $instance = $resolver;
        } else {
            $instance = self::construct($resolver, $params);
        }

        if ($binding['singleton']) {
            self::$instances[$serviceId] = $instance;
        }

        return $instance;
    }

    public static function makeWith(string $key, array $params): mixed
    {
        return self::make($key, $params);
    }

    public static function reset(): void
    {
        self::$bindings = [];
        self::$instances = [];
        self::$contracts = [];
    }

    private static function construct(string $class, array $params): object
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Service {$class} is not available.");
        }

        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new InvalidArgumentException("Service {$class} cannot be instantiated.");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $index => $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $params)) {
                $arguments[] = $params[$name];
                continue;
            }
            if (array_key_exists($index, $params)) {
                $arguments[] = $params[$index];
                continue;
            }

            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = self::make($type->getName());
                continue;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new InvalidArgumentException("Service {$class} requires parameter \${$name}.");
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
