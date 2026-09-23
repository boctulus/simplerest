<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$environmentPath = $projectRoot . DIRECTORY_SEPARATOR . '.env';
$examplePath = $projectRoot . DIRECTORY_SEPARATOR . '.env.example';

if (is_file($environmentPath)) {
    exit(0);
}

$environment = @file_get_contents($examplePath);
if ($environment === false) {
    fwrite(STDERR, "Unable to read .env.example; .env was not created.\n");
    exit(1);
}

$environment = preg_replace_callback(
    '/^((?:export[ \t]+)?([A-Z][A-Z0-9_]*)[ \t]*=)(?:"(?:\\\\.|[^"\\\\])*"|\x27[^\x27]*\x27|[^\r\n]*)/mis',
    static function (array $match): string {
        if (preg_match('/(?:PASSWORD|SECRET|TOKEN|API_KEY|PRIVATE_KEY)/i', $match[2]) === 1) {
            return $match[1];
        }

        return $match[0];
    },
    $environment
);

if ($environment === null) {
    fwrite(STDERR, "Unable to sanitize .env.example; .env was not created.\n");
    exit(1);
}

$handle = @fopen($environmentPath, 'x');
if ($handle === false) {
    if (is_file($environmentPath)) {
        exit(0);
    }

    fwrite(STDERR, "Unable to create .env.\n");
    exit(1);
}

$written = fwrite($handle, $environment);
$closed = fclose($handle);
if ($written !== strlen($environment) || !$closed) {
    @unlink($environmentPath);
    fwrite(STDERR, "Unable to finish writing .env.\n");
    exit(1);
}

fwrite(STDOUT, "Created .env from .env.example with secret-like values cleared.\n");
