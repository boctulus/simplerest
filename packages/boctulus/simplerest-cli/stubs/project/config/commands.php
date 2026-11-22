<?php

/*
 * CLI Command Registry
 *
 * 'packages'  → enabled Composer packages that provide commands in
 *               vendor/{vendor}/{package}/src/Commands/. An enabled package
 *               that is not installed is skipped.
 * 'paths'     → extra directories with group sub-folders (like app/Commands/).
 * 'overrides' → command/alias key => provider ID. Default precedence is
 *               application (app/Commands) > enabled packages > framework.
 */

return [
    'paths' => [],

    'packages' => [
        'boctulus/simplerest-cli',
        'boctulus/simplerest-scaffolding',
    ],

    'framework_paths' => [],

    'overrides' => [],

    'cross_aliases' => [],
];
