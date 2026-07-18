<?php

/*
    CORS Configuration

    Here you may configure your settings for cross-origin resource sharing
    or "CORS". This determines what cross-origin operations may execute
*/

return [    

    'paths' => ['api/*', /* .. */],

    'allowedMethods' => ['*'],

    'allowedOrigins' => ['*'],

    'allowedOriginsPatterns' => [],

    'allowedHeaders' => ['*'],

    'exposedHeaders' => ['Accept-Query'],

    'maxAge' => 0,

    'supportsCredentials' => false,
];
