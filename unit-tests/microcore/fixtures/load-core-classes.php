<?php

declare(strict_types=1);

// Loads every class, interface, trait and enum declared under src/framework
// using ONLY a PSR-4 autoloader for the core: no vendor/, no packages.
// Any dependency on an external library or package fails here.

$core = rtrim($argv[1], '/\\') . '/src/framework/';
$prefix = 'Boctulus\\Simplerest\\Core\\';

spl_autoload_register(static function (string $class) use ($core, $prefix): void {
    if (str_starts_with($class, $prefix)) {
        $file = $core . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($core, FilesystemIterator::SKIP_DOTS));
$loaded = 0;
foreach ($iterator as $file) {
    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($core)));
    // Templates are code-generation sources and Helpers declare functions only.
    if ($file->getExtension() !== 'php' || preg_match('~^(Templates|Helpers)/~', $relative)) {
        continue;
    }
    if (!preg_match('/^\s*(?:(?:abstract|final|readonly)\s+)*(?:class|interface|trait|enum)\s+\w+/m', (string) file_get_contents($file->getPathname()))) {
        continue;
    }
    $symbol = $prefix . str_replace('/', '\\', substr($relative, 0, -4));
    if (!class_exists($symbol) && !interface_exists($symbol) && !trait_exists($symbol) && !enum_exists($symbol)) {
        fwrite(STDERR, "Core symbol not declared by its PSR-4 file: $symbol\n");
        exit(1);
    }
    $loaded++;
}

echo "loaded=$loaded\n";
