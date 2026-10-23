<?php

// Application-only bootstrap for the pre-microcore CLI. These helpers live
// outside the public Composer package, which deliberately has no helper autoload.
return [
    'include' => [
        __DIR__ . '/../archive/simplerest-framework-extractions-2026-10-23/Helpers',
        __DIR__ . '/../app/Helpers',
    ],
    'exclude' => [],
];
