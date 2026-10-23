<?php

declare(strict_types=1);

$autoloadFile = null;
foreach ([dirname(__DIR__, 4), dirname(__DIR__)] as $projectRoot) {
    $candidate = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($candidate)) {
        $autoloadFile = $candidate;
        break;
    }
}

if ($autoloadFile === null) {
    throw new RuntimeException('Composer autoload file is missing from the monorepo, package checkout, and consumer paths.');
}

require_once $autoloadFile;

if (!defined('ETC_PATH')) {
    define(
        'ETC_PATH',
        sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-pdf-package-tests-' . bin2hex(random_bytes(8))
    );
}
