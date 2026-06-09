#!/usr/bin/env php
<?php

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Libs\CommandRegistry;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

/*
   Parse env: and cfg: overrides from the CLI.

   Examples:
     php com my_cmd my_action env:variable=value
     php com my_cmd my_action cfg:my_config_var=3
*/
parse_str(implode('&', array_slice($_SERVER['argv'], 3)), $_GET);

foreach ($_GET as $var => $val) {
    $pos = strpos($var, 'env:');
    if ($pos === 0) {
        $var = substr($var, 4);
        Env::set($var, $val);
        continue;
    }

    $pos = strpos($var, 'cfg:');
    if ($pos === 0) {
        $var = substr($var, 4);
        Config::set($var, $val);
    }
}

// -------------------------------------------------------------------------
// Argument pre-processing
// -------------------------------------------------------------------------

$args    = array_slice($argv, 1);
$isDebug = in_array('--debug', $args) || in_array('-d', $args);

// Strip global flags so they don't interfere with group/command resolution.
$cleanArgs = array_values(
    array_filter($args, static fn($a) => $a !== '--debug' && $a !== '-d')
);

// -------------------------------------------------------------------------
// No arguments → show all groups
// -------------------------------------------------------------------------

if (count($cleanArgs) === 0) {
    $registry = CommandRegistry::init($isDebug);
    $registry->showAllGroups();
    exit(0);
}

$group = $cleanArgs[0];

// -------------------------------------------------------------------------
// 'help' pseudo-group
// -------------------------------------------------------------------------

if ($group === 'help') {
    $registry      = CommandRegistry::init($isDebug);
    $targetGroup   = $cleanArgs[1] ?? null;
    $targetCommand = $cleanArgs[2] ?? null;
    $registry->showHelp($targetGroup, $targetCommand);
    exit(0);
}

// -------------------------------------------------------------------------
// Registry-based dispatch (new engine)
// -------------------------------------------------------------------------

$registry = CommandRegistry::init($isDebug);

if ($registry->hasGroup($group)) {
    $subArgs = array_slice($cleanArgs, 1);
    $registry->dispatch($group, $subArgs);
    exit(0);
}

// -------------------------------------------------------------------------
// Fallback: CliRouter / FrontController (for web-route invocations via CLI)
// -------------------------------------------------------------------------

$cfg     = Config::get();
$handled = false;

if (!empty($cfg['console_router'])) {
    include CONFIG_PATH . 'cli_routes.php';
    CliRouter::compile();
    $handled = CliRouter::resolve();
}

if (!$handled && !empty($cfg['front_controller'])) {
    FrontController::resolve();
}
