<?php

/*
    Package Configuration

    This configuration file is specific to the package and overrides
    global configuration when a controller from this package is executed.

    Available options:
    - front_controller: Enable/disable FrontController for this package (default: true)
    - web_router: Enable/disable WebRouter for this package (default: true)
    - console_router: Enable/disable CliRouter for this package (default: true)
    - base_url: Base URL prefix for package routes (default: '')
    - namespace: Package namespace (auto-detected from ServiceProvider)

    Example:
    return [
        'front_controller' => true,  // Enable FrontController
        'web_router' => true,        // Enable WebRouter
        'console_router' => true,    // Enable CliRouter
        'base_url' => '/mypackage',  // Optional base URL
    ];
*/

return [
    // Enable or disable FrontController for this package
    'front_controller' => true,

    // Enable or disable WebRouter for this package
    'web_router' => true,

    // Enable or disable CliRouter for this package
    'console_router' => true,

    // Package-specific settings
    // Add your custom configuration here
];
