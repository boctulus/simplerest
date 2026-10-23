<?php

// Legacy application-only bootstrap for the pre-microcore CLI.
// This development checkout stores old helpers under app/LegacyCore, outside the public Composer core.
// PACK's explicit allowlist excludes them from every Composer core release.
// When CLI/helpers are moved to their own package, update this path with tests.
return [
    'include' => [
        __DIR__ . '/../app/LegacyCore/Helpers',
        __DIR__ . '/../app/Helpers',
    ],
    'exclude' => [],
];
