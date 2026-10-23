<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

final class Config
{
    private static array $data = [];

    public static function load(array $values): void
    {
        self::$data = $values;
    }

    public static function get(?string $property = null, mixed $default = null): mixed
    {
        if ($property === null || $property === '' || $property === '/' || $property === '.') {
            return self::$data;
        }

        $value = self::$data;
        foreach (explode('.', $property) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    public static function set(string $property, mixed $value): void
    {
        if ($property === '' || $property === '/' || $property === '.') {
            if (!is_array($value)) {
                throw new \InvalidArgumentException('Root configuration must be an array.');
            }
            self::$data = $value;
            return;
        }

        $keys = explode('.', $property);
        $cursor = &self::$data;
        foreach ($keys as $key) {
            if (!isset($cursor[$key]) || !is_array($cursor[$key])) {
                $cursor[$key] = [];
            }
            $cursor = &$cursor[$key];
        }
        $cursor = $value;
    }

    public static function add(string $property, mixed $value): void
    {
        $current = self::get($property, []);
        if (!is_array($current)) {
            $current = [$current];
        }
        $current[] = $value;
        self::set($property, $current);
    }

    public static function reset(): void
    {
        self::$data = [];
    }
}
