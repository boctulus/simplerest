<?php
/*
 * Test script to directly test OpenFacturaController methods with proper response handling
 */

use Boctulus\FriendlyposWeb\Controllers\OpenFacturaController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/app.php';

// Create a mock request to initialize the framework properly
$request = Request::getInstance();

echo "Testing OpenFacturaController methods directly\n";
echo "=============================================\n\n";

echo "Creating controller instance...\n";
$controller = new OpenFacturaController();

echo "Testing health method directly...\n";

// Temporarily capture output to see what the controller method returns
ob_start();
try {
    $result = $controller->health();
    $output = ob_get_contents();
    ob_end_clean();
    
    if ($result instanceof Response) {
        echo "Method returned Response instance\n";
        echo "Response is empty: " . ($result->isEmpty() ? 'YES' : 'NO') . "\n";
        
        // Try to get the raw data
        $reflection = new ReflectionClass($result);
        $property = $reflection->getProperty('data');
        $property->setAccessible(true);
        $data = $property->getValue($result);
        
        echo "Raw response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Method returned: " . gettype($result) . "\n";
        if (is_object($result)) {
            echo "Object class: " . get_class($result) . "\n";
        }
    }

    if (!empty($output)) {
        echo "Output captured: $output\n";
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTesting error method directly...\n";

ob_start();
try {
    $result = $controller->getDTEStatus('invalid_token');
    $output = ob_get_contents();
    ob_end_clean();
    
    if ($result instanceof Response) {
        echo "Method returned Response instance\n";
        echo "Response is empty: " . ($result->isEmpty() ? 'YES' : 'NO') . "\n";
        
        // Try to get the raw data
        $reflection = new ReflectionClass($result);
        $property = $reflection->getProperty('data');
        $property->setAccessible(true);
        $data = $property->getValue($result);
        
        echo "Raw response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Method returned: " . gettype($result) . "\n";
        if (is_object($result)) {
            echo "Object class: " . get_class($result) . "\n";
        }
    }

    if (!empty($output)) {
        echo "Output captured: $output\n";
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTesting with custom API key override...\n";

// Test with header override
function setMockRequestHeaders($headers) {
    $request = Request::getInstance();
    $reflection = new ReflectionClass($request);
    
    // Set headers property to our mock headers
    $headersProperty = $reflection->getProperty('headers');
    $headersProperty->setAccessible(true);
    
    // Normalize headers for the format expected by the controller
    $normalizedHeaders = [];
    foreach ($headers as $k => $v) {
        $normalizedHeaders[strtolower($k)] = $v;
    }
    
    $headersProperty->setValue($request, $normalizedHeaders);
}

setMockRequestHeaders([
    'X-Openfactura-Api-Key' => 'test-key',
    'X-Openfactura-Sandbox' => 'true'
]);

ob_start();
try {
    $result = $controller->health();
    $output = ob_get_contents();
    ob_end_clean();
    
    if ($result instanceof Response) {
        echo "Method returned Response instance\n";
        echo "Response is empty: " . ($result->isEmpty() ? 'YES' : 'NO') . "\n";
        
        // Try to get the raw data
        $reflection = new ReflectionClass($result);
        $property = $reflection->getProperty('data');
        $property->setAccessible(true);
        $data = $property->getValue($result);
        
        echo "Raw response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Method returned: " . gettype($result) . "\n";
        if (is_object($result)) {
            echo "Object class: " . get_class($result) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nTest completed!\n";