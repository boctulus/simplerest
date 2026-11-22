<?php

/*
    Registers GitHub repositories as new Packagist packages, or asks Packagist
    to re-crawl packages that already exist.

    Credentials come from .env (never from arguments, so they do not end up in
    shell history):

        PACKAGIST_USERNAME=<packagist user>
        PACKAGIST_API_TOKEN=<Main API Token>   # create needs the main token;
                                                # update also accepts the safe token

    Usage (dry run unless --confirm is given):

        php scripts/composer/packagist-register.php create https://github.com/<owner>/<repo> [...] [--confirm]
        php scripts/composer/packagist-register.php update https://github.com/<owner>/<repo> [...] [--confirm]

    The token is sent in the Authorization header, never in the URL, and is
    never printed.
*/

use Boctulus\Simplerest\Core\Libs\ApiClient;

if (PHP_SAPI !== 'cli') {
    exit(1);
}

chdir(dirname(__DIR__, 2));
require_once dirname(__DIR__, 2) . '/app.php';

$args = array_slice($argv, 1);
$confirm = in_array('--confirm', $args, true);
$args = array_values(array_filter($args, static fn (string $arg): bool => $arg !== '--confirm'));
$action = array_shift($args);
$repositories = $args;

if (!in_array($action, ['create', 'update'], true) || $repositories === []) {
    fwrite(STDERR, "Usage: php scripts/composer/packagist-register.php create|update <github-url> [...] [--confirm]" . PHP_EOL);
    exit(1);
}

foreach ($repositories as $url) {
    if (preg_match('~\Ahttps://github\.com/[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+\z~', $url) !== 1) {
        fwrite(STDERR, "Not a GitHub repository URL: {$url}" . PHP_EOL);
        exit(1);
    }
}

$username = (string) env('PACKAGIST_USERNAME', '');
$token = (string) env('PACKAGIST_API_TOKEN', '');

if ($username === '' || $token === '') {
    fwrite(STDERR, 'Set PACKAGIST_USERNAME and PACKAGIST_API_TOKEN in .env first.' . PHP_EOL);
    exit(1);
}

$endpoint = $action === 'create'
    ? 'https://packagist.org/api/create-package'
    : 'https://packagist.org/api/update-package';

$failed = 0;

foreach ($repositories as $url) {
    if (!$confirm) {
        echo "DRY RUN  {$action} {$url}" . PHP_EOL;
        continue;
    }

    $client = ApiClient::instance($endpoint)
        ->contentType('application/json')
        ->accept('application/json')
        ->authorization("Bearer {$username}:{$token}")
        ->setBody(['repository' => ['url' => $url]]);

    // Verify TLS against the operating system's CA store as well: PHP's bundled
    // CA list fails where a local proxy or antivirus re-signs HTTPS traffic.
    if (defined('CURLSSLOPT_NATIVE_CA')) {
        $client->setOption(CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);
    }

    $client->post();

    if ($client->getError()) {
        fwrite(STDERR, "Transport error for {$url}: " . $client->getError() . PHP_EOL);
    }

    $status = (int) $client->getStatus();
    $body = $client->getRawResponse();
    $decoded = is_string($body) ? json_decode($body, true) : null;
    $summary = is_array($decoded) ? json_encode($decoded, JSON_UNESCAPED_SLASHES) : trim((string) $body);
    $summary = str_replace($token, '***', (string) $summary);

    $ok = $status >= 200 && $status < 300 && (!is_array($decoded) || ($decoded['status'] ?? 'success') === 'success');
    if (!$ok) {
        $failed++;
    }

    echo ($ok ? 'OK       ' : 'FAILED   ') . "{$action} {$url} (HTTP {$status}) " . substr($summary, 0, 300) . PHP_EOL;
}

if (!$confirm) {
    echo 'Nothing was sent. Re-run with --confirm.' . PHP_EOL;
}

exit($failed > 0 ? 1 : 0);
