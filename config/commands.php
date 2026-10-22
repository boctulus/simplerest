<?php

/*
 * CLI Command Registry — additional search paths
 *
 * 'paths'    → arbitrary directories that contain group sub-folders
 *              (same structure as app/Commands/)
 *              Relative paths are resolved from ROOT_PATH.
 *
 * 'packages' → explicitly enabled Composer package IDs. The registry looks in
 *              vendor/{vendor}/{package}/src/Commands/ or the monorepo package
 *              path during development.
 *
 * 'overrides' → command/alias key => provider ID. Default precedence is host
 *               application > enabled extensions > framework paths. The host
 *               provider is 'application'. Same-priority collisions fail.
 *
 * Example:
 *
 *   'paths'    => ['some/custom/Commands'],
 *   'packages' => ['boctulus/zippy', 'boctulus/dummyapi'],
 */

return [
    'paths'    => [],

    'packages' => [
        'boctulus/simplerest-code-tools',
    ],

    'framework_paths' => [],

    'overrides' => [],

    /*
     * Cross-group aliases
     *
     * 'source_group source_command' => 'target_group target_command'
     *
     * Makes "php com <source_group> <source_command>" behave identically to
     * "php com <target_group> <target_command>", including --help and all args.
     * The alias appears in the source group's help output.
     */
    'cross_aliases' => [
        'make acl' => 'acl make',
    ],
];
