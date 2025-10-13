<?php

/*
    Test runner for Typeform module
    
    Usage: php run_tests.php
*/

// Include WordPress and plugin bootstrap
require_once __DIR__ . '/../../../../../../wp-config.php';
require_once __DIR__ . '/../../../../../../../wp-load.php';

// Include the test class
require_once __DIR__ . '/TypeformApiTest.php';

use Boctulus\Simplerest\modules\Typeform\tests\TypeformApiTest;

echo "Typeform Module Test Suite\n";
echo "==========================\n\n";

// Check if plugin is active
if (!is_plugin_active('efirma/index.php')) {
    echo "❌ Error: eFirma plugin is not active!\n";
    exit(1);
}

// Run tests
$testSuite = new TypeformApiTest();
$success = $testSuite->runAllTests();

if ($success) {
    echo "\n🎉 All tests completed successfully!\n";
    exit(0);
} else {
    echo "\n💥 Some tests failed. Check output above.\n";
    exit(1);
}