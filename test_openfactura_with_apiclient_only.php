<?php
/*
 * Test script to verify OpenFacturaController functionality using ApiClient (without direct cURL functions)
 * This script tests the actual HTTP endpoints using the framework's ApiClient correctly
 */

require_once __DIR__ . '/app.php';

use Boctulus\Simplerest\Core\Libs\ApiClient;

// Define the base URL from the environment or default
$baseUrl = getenv('APP_URL') ?: 'http://simplerest.lan';

echo "Testing OpenFacturaController Functionality with ApiClient\n";
echo "========================================================\n\n";
echo "Base URL: $baseUrl\n\n";

// Test function to make API requests using ApiClient
function makeRequest($method, $endpoint, $data = null, $headers = []) {
    global $baseUrl;
    
    $url = $baseUrl . $endpoint;
    
    // Create ApiClient instance with additional options
    $client = new ApiClient();
    
    // Configure the client
    $client
        ->disableSSL()  // Disable SSL verification for local testing
        ->followLocations(); // Follow redirects
    
    // Set headers if provided
    if (!empty($headers)) {
        $client->setHeaders($headers);
    }
    
    // Set body data if provided
    if ($data) {
        $client->setBody($data);
    }
    
    // Make the request based on the method
    switch (strtoupper($method)) {
        case 'GET':
            $client->get($url);
            break;
        case 'POST':
            $client->post($url);
            break;
        case 'PUT':
            $client->put($url);
            break;
        case 'PATCH':
            $client->patch($url);
            break;
        case 'DELETE':
            $client->delete($url);
            break;
        default:
            $client->request($url, $method);
            break;
    }
    
    // Get the response details
    $status = $client->status();
    $error = $client->error();
    $data = $client->data(); // Get the parsed data
    $fullResponse = $client->getResponse(); // Get the full response
    
    return [
        'status' => $status,
        'error' => $error,
        'data' => $data,
        'full_response' => $fullResponse,
        'raw_body' => $client->getBody()
    ];
}

// Test 1: Health Check
echo "1. Testing Health Check Endpoint\n";
echo "-------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/health');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 2: Emit DTE - Should fail without proper data
echo "2. Testing Emit DTE Endpoint (should fail without dteData)\n";
echo "--------------------------------------------------------\n";
$result = makeRequest('POST', '/api/v1/openfactura/dte/emit', ['invalid_data' => 'test']);
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 3: Get DTE Status - Should fail with invalid token
echo "3. Testing Get DTE Status Endpoint (should fail with invalid token)\n";
echo "------------------------------------------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/dte/status/invalid_token');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 4: Anular Guia Despacho - Should fail without proper data
echo "4. Testing Anular Guia Despacho Endpoint (should fail without valid data)\n";
echo "-----------------------------------------------------------------------\n";
$result = makeRequest('POST', '/api/v1/openfactura/dte/anular-guia', [
    'folio' => 12345,
    'fecha' => '2025-01-15'
]);
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 5: Anular DTE - Should fail without proper data
echo "5. Testing Anular DTE Endpoint (should fail without valid data)\n";
echo "-------------------------------------------------------------\n";
$result = makeRequest('POST', '/api/v1/openfactura/dte/anular', [
    'dteData' => [
        'Encabezado' => [
            'IdDoc' => [
                'TipoDTE' => 61
            ]
        ]
    ]
]);
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 6: Get Taxpayer - Should fail without valid RUT
echo "6. Testing Get Taxpayer Endpoint (should fail with invalid RUT)\n";
echo "-------------------------------------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/taxpayer/12345678-9');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 7: Get Organization
echo "7. Testing Get Organization Endpoint\n";
echo "-----------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/organization');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 8: Get Sales Registry
echo "8. Testing Get Sales Registry Endpoint (should fail without valid year/month)\n";
echo "-------------------------------------------------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/sales-registry/2025/01');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 9: Get Purchase Registry
echo "9. Testing Get Purchase Registry Endpoint (should fail without valid year/month)\n";
echo "---------------------------------------------------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/purchase-registry/2025/01');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 10: Get Document
echo "10. Testing Get Document Endpoint (should fail with invalid parameters)\n";
echo "---------------------------------------------------------------------\n";
$result = makeRequest('GET', '/api/v1/openfactura/document/12345678-9/33/12345');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 11: Testing with custom headers
echo "11. Testing Endpoint with Custom Headers\n";
echo "---------------------------------------\n";
$headers = [
    'X-Openfactura-Api-Key' => 'test_key',
    'X-Openfactura-Sandbox' => 'true',
    'Content-Type' => 'application/json'
];
$result = makeRequest('GET', '/api/v1/openfactura/health', null, $headers);
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

echo "All tests completed!\n";
echo "Remember: For the tests to work properly, you need:\n";
echo "1. A running web server with the SimpleRest framework properly configured\n";
echo "2. Properly configured OpenFactura API keys in your .env file\n";
echo "3. The friendlypos-web package properly registered and loaded\n";
echo "4. Apache/Nginx rewrite rules to route requests through index.php\n";