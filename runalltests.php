<?php

/**
 * Test Runner Script
 * Executes all test files sequentially and reports overall success or failure
 */

// Define the test files to run - comment out any tests you want to skip
$testFiles = [
    // Core tests
    'tests/DB_TransactionTest.php',
    'tests/ExampleTest.php',
    'tests/ModelTest.php',    
    // 'tests/QueryBuilderExtendedTest.php',
    // 'tests/RequestImmutableMethodsTest.php',
    'tests/ResponseImmutableMethodsTest.php',
    
    'tests/ValidatorTest.php',
    'tests/WebRouterTest.php',
    'tests/WebRouterFunctionalTest.php',
    
    /*

    // API tests
    'tests/ApiTest.php',
    
    // Auth tests
    'tests/AuthTest.php',
    
    // Command tests
    'tests/CommandTraitTest.php',
    
    // ORM tests
    'tests/SimpleORMTest.php',
    
    // Unit tests
    'tests/Unit/TranslateTest.php',
    
    // Unit tests
    'tests/unit-tests/Psr7AdaptersTest.php',
    
    */
];

// Initialize counters
$totalTests = count($testFiles);
$passedTests = 0;
$failedTests = 0;
$executedTests = 0;

echo "Starting test execution...\n";
echo "Total test files to execute: $totalTests\n\n";

// Execute each test file
foreach ($testFiles as $testFile) {
    if (!file_exists($testFile)) {
        echo "SKIPPED: $testFile (File does not exist)\n";
        continue;
    }
    
    echo "Running: $testFile\n";
    
    // Execute the test file using PHPUnit
    $command = "php ./vendor/bin/phpunit --bootstrap vendor/autoload.php " . escapeshellarg($testFile) . " 2>&1";
    $output = [];
    $returnCode = 0;

    exec($command, $output, $returnCode);
    
    $outputString = implode("\n", $output);
    
    if ($returnCode === 0) {
        echo "PASSED: $testFile\n";
        $passedTests++;
    } else {
        echo "FAILED: $testFile\n";
        $failedTests++;
        // Optionally show the output for failed tests
        // echo "Output:\n$outputString\n";
    }
    
    $executedTests++;
    echo "\n";
}

// Summary
echo "=====================\n";
echo "EXECUTION SUMMARY\n";
echo "=====================\n";
echo "Total tests defined: $totalTests\n";
echo "Tests executed: $executedTests\n";
echo "Tests passed: $passedTests\n";
echo "Tests failed: $failedTests\n";

if ($failedTests > 0) {
    echo "\nOVERALL RESULT: FAILURE\n";
    echo "Some tests failed during execution.\n";
    exit(1); // Exit with error code
} else {
    echo "\nOVERALL RESULT: SUCCESS\n";
    echo "All tests passed!\n";
    exit(0); // Exit successfully
}