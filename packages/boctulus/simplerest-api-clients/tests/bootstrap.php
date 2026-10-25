<?php

declare(strict_types=1);

$autoloadFile = null;
foreach ([dirname(__DIR__), dirname(__DIR__, 4)] as $projectRoot) {
    $candidate = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($candidate)) {
        $autoloadFile = $candidate;
        break;
    }
}

if ($autoloadFile === null) {
    throw new RuntimeException('Composer autoload file is missing from the package checkout, consumer project, or monorepo.');
}

require_once $autoloadFile;

if (!function_exists('is_cli')) {
    function is_cli(): bool
    {
        return PHP_SAPI === 'cli';
    }
}
