<?php

// Legacy application-only bootstrap for the pre-microcore CLI.
// Helpers live in the canonical src/framework tree of this development checkout.
// PACK's explicit allowlist excludes them from every Composer core release.
// When CLI/helpers are moved to their own package, update this path with tests.
return [
    'include' => [
        __DIR__ . '/../src/framework/Helpers',
        __DIR__ . '/../app/Helpers',
    ],
    'exclude' => [],
];
