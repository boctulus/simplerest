<?php

declare(strict_types=1);

/**
 * Explicit legacy-application runtime loader. This is NOT part of the
 * Composer HTTP microcore and is never loaded by microcore-only consumers.
 *
 * Former framework implementations remain application-owned during the
 * migration. No optional-package aliases or fallbacks enter src/framework.
 */
$legacyRoot = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
$prefix = 'Boctulus\\Simplerest\\Core\\';
$originalImplementations = $legacyRoot . 'app/LegacyCore/';
$extractedImplementations = $legacyRoot . 'archive/simplerest-framework-extractions-2026-10-23/';

spl_autoload_register(
    static function (string $class) use ($prefix, $originalImplementations, $extractedImplementations): void {
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        foreach ([$originalImplementations, $extractedImplementations] as $directory) {
            $path = $directory . $relative;
            if (is_file($path)) {
                require_once $path;
                return;
            }
        }
    },
    true,
    true
);
