<?php

use Boctulus\Simplerest\Core\Libs\ApiClient;
/*
 * Test script to verify OpenFacturaController functionality
 * This script tests the actual HTTP endpoints using cURL
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/app.php';


// Define the base URL from the environment or default
$baseUrl = env('APP_URL', 'http://simplerest.lan');

echo "Testing OpenFacturaController Functionality\n";
echo "=========================================\n\n";
echo "Base URL: $baseUrl\n\n";

// Test function to make API requests
function makeRequest($method, $endpoint, $data = null, $headers = []) {
    global $baseUrl;

    $url = $baseUrl . '/' . ltrim($endpoint, '/');

    $client = new ApiClient();

    if (!isset($headers['Content-Type'])) {
        $headers['Content-Type'] = 'application/json';
    }

    // Enable JSON encoding for request body
    if ($data !== null) {
        $client->setBody($data, true);
    }

    $client->request($url, $method, null, $headers);

    $error = $client->getError();

    return [
        'success' => empty($error),
        'http_code' => $client->getStatus(),
        'error' => $client->getError(),
        'response' => $client->getResponse(true),
        'raw_response' => $client->getRawResponse()
    ];
}

// dd(
//     makeRequest('GET', '/dumb/now')
// );
// exit;

// Test 1: Health Check - This should work without API key
echo "1. Testing Health Check Endpoint\n";
echo "-------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/health');

if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    echo "Raw Response: {$result['raw_response']}\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 2: Emit DTE - Should fail without proper data
echo "2. Testing Emit DTE Endpoint\n";
echo "---------------------------\n";
$result = makeRequest('POST', '/api/openfactura/dte/emit', ['dteData' => []]);
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 3: Get DTE Status - Should fail without token
echo "3. Testing Get DTE Status Endpoint\n";
echo "----------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/dte/status/invalid_token');
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 4: Anular Guia Despacho - Should fail without proper data
echo "4. Testing Anular Guia Despacho Endpoint\n";
echo "---------------------------------------\n";
$result = makeRequest('POST', '/api/openfactura/dte/anular-guia', [
    'folio' => 12345,
    'fecha' => '2025-01-15'
]);
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 5: Anular DTE - Should fail without proper data
echo "5. Testing Anular DTE Endpoint\n";
echo "-----------------------------\n";
$result = makeRequest('POST', '/api/openfactura/dte/anular', [
    'dteData' => [
        'Encabezado' => [
            'IdDoc' => [
                'TipoDTE' => 61
            ]
        ]
    ]
]);
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 6: Get Taxpayer - Should fail without valid RUT
echo "6. Testing Get Taxpayer Endpoint\n";
echo "-------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/taxpayer/12345678-9');
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 7: Get Organization
echo "7. Testing Get Organization Endpoint\n";
echo "-----------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/organization');
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 8: Get Sales Registry
echo "8. Testing Get Sales Registry Endpoint\n";
echo "-------------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/sales-registry/2025/01');
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 9: Get Purchase Registry
echo "9. Testing Get Purchase Registry Endpoint\n";
echo "----------------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/purchase-registry/2025/01');
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 10: Get Document
echo "10. Testing Get Document Endpoint\n";
echo "---------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/document/12345678-9/33/12345');
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

// Test 11: Testing with custom API key header
echo "11. Testing Endpoint with Custom API Key Header\n";
echo "----------------------------------------------\n";
$headers = [
    'X-Openfactura-Api-Key: your_test_api_key_here',
    'X-Openfactura-Sandbox: true'
];
$result = makeRequest('GET', '/api/openfactura/health', null, $headers);
if ($result['success']) {
    echo "Status: {$result['http_code']}\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: {$result['error']}\n";
}
echo "\n";

echo "All tests completed!\n";
echo "Remember: For the tests to work properly, you need:\n";
echo "1. A running web server with the SimpleRest framework\n";
echo "2. Properly configured OpenFactura API keys in your .env file\n";
echo "3. The friendlypos-web package properly registered and loaded\n";