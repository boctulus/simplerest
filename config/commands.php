<?php

/*
 * CLI Command Registry — additional search paths
 *
 * 'paths'    → arbitrary directories that contain group sub-folders
 *              (same structure as app/Commands/)
 *              Relative paths are resolved from ROOT_PATH.
 *
 * 'packages' → 'vendor/package' entries; the registry will look in
 *              packages/{vendor}/{package}/src/Commands/
 *              for group sub-folders automatically.
 *
 * Example:
 *
 *   'paths'    => ['some/custom/Commands'],
 *   'packages' => ['boctulus/zippy', 'boctulus/dummyapi'],
 */

return [
    'paths'    => [],

    'packages' => [
        // 'boctulus/zippy',  // interfiere con "php com users list" (BUG)
    ],

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
