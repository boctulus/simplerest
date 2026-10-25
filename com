#!/usr/bin/env php
<?php

// The Composer microcore packer must not boot the legacy application.
if (($argv[1] ?? '') === 'pack') {
    $packAction = $argv[2] ?? '';
    if (in_array($packAction, ['build', 'run', 'package', 'dry-run'], true)) {
        require_once __DIR__ . '/scripts/pack/build-core.php';
        $packArguments = array_slice($argv, 3);
        if ($packAction === 'dry-run' && !in_array('--dry-run', $packArguments, true)) {
            array_unshift($packArguments, '--dry-run');
        }
        exit(SimpleRestCorePackBuilder::main($packArguments));
    }
}

require_once __DIR__ . '/app.php';

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\Libs\CommandRegistry;
use Boctulus\Simplerest\Core\Libs\Config;

$args = array_values(array_slice($argv, 1));
$isDebug = in_array('--debug', $args, true) || in_array('-d', $args, true);
$args = array_values(array_filter($args, static fn (string $arg): bool => !in_array($arg, ['--debug', '-d'], true)));

$registry = CommandRegistry::init($isDebug);

if ($args === []) {
    $registry->showAllGroups();
    exit(0);
}

if ($args[0] === 'help') {
    $registry->showHelp($args[1] ?? null, $args[2] ?? null);
    exit(0);
}

if ($registry->hasGroup($args[0])) {
    $registry->dispatch($args[0], array_slice($args, 1));
    exit(0);
}

$config = Config::get();
if (!empty($config['console_router'])) {
    require CONFIG_PATH . 'cli_routes.php';
    CliRouter::compile();
    if (CliRouter::resolve()) {
        exit(0);
    }
}

if (!empty($config['front_controller'])) {
    FrontController::resolve();
}
