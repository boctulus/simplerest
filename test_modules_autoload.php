<?php

/**
 * Test script to verify that modules can be loaded without composer.json entries
 *
 * This script tests:
 * 1. Module classes can be autoloaded
 * 2. ModuleProvider can be instantiated
 * 3. Module controllers can be autoloaded
 */

echo "Testing Module Autoloader...\n\n";

require_once __DIR__ . '/app.php';

// Test 1: Load Xeni ModuleProvider
echo "Test 1: Loading Xeni ModuleProvider...\n";
try {
    $provider_class = 'Boctulus\Simplerest\Modules\Xeni\ModuleProvider';

    if (class_exists($provider_class)) {
        echo "✓ Class $provider_class loaded successfully\n";

        $provider = new $provider_class();
        echo "✓ ModuleProvider instantiated successfully\n";
    } else {
        echo "✗ Failed to load class $provider_class\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Load Xeni TestController
echo "Test 2: Loading Xeni TestController...\n";
try {
    $controller_class = 'Boctulus\Simplerest\Modules\Xeni\Controllers\TestController';

    if (class_exists($controller_class)) {
        echo "✓ Class $controller_class loaded successfully\n";
    } else {
        echo "✗ Failed to load class $controller_class\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Load Xeni V1TestController
echo "Test 3: Loading Xeni V1TestController...\n";
try {
    $controller_class = 'Boctulus\Simplerest\Modules\Xeni\Controllers\V1TestController';

    if (class_exists($controller_class)) {
        echo "✓ Class $controller_class loaded successfully\n";
    } else {
        echo "✗ Failed to load class $controller_class\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Load Xeni XeniSdk
echo "Test 4: Loading Xeni XeniSdk...\n";
try {
    $lib_class = 'Boctulus\Simplerest\Modules\Xeni\Libs\XeniSdk';

    if (class_exists($lib_class)) {
        echo "✓ Class $lib_class loaded successfully\n";
    } else {
        echo "✗ Failed to load class $lib_class\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Verify provider is registered in config
echo "Test 5: Checking if Xeni ModuleProvider is registered in config...\n";
$config = include __DIR__ . '/config/config.php';
$provider_registered = in_array('Boctulus\Simplerest\Modules\Xeni\ModuleProvider', $config['providers']);

if ($provider_registered) {
    echo "✓ Xeni ModuleProvider is registered in config.php\n";
} else {
    echo "✗ Xeni ModuleProvider is NOT registered in config.php\n";
}

echo "\n=== All tests completed ===\n";
