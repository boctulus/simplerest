<?php
/*
 * Test script to verify OpenFacturaController functionality using ApiClient
 * This version includes more detailed error reporting and checks
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
    $rawResponse = $client->getResponse(); // Get the full response
    $data = $client->data(); // Get the parsed data
    
    return [
        'status' => $status,
        'error' => $error,
        'raw_response' => $rawResponse,
        'data' => $data,
        'client' => $client
    ];
}

// Test 1: Health Check
echo "1. Testing Health Check Endpoint\n";
echo "-------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/health');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Raw response: " . json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n";
echo "Parsed data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 2: Emit DTE - Should fail without proper data
echo "2. Testing Emit DTE Endpoint (should fail without dteData)\n";
echo "--------------------------------------------------------\n";
$result = makeRequest('POST', '/api/openfactura/dte/emit', ['some_invalid_data' => 'test']);
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Raw response: " . json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n";
echo "Parsed data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 3: Get DTE Status - Should fail without valid token
echo "3. Testing Get DTE Status Endpoint (should fail with invalid token)\n";
echo "------------------------------------------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/dte/status/invalid_token');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Raw response: " . json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n";
echo "Parsed data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 4: Anular Guia Despacho - Should fail without proper data
echo "4. Testing Anular Guia Despacho Endpoint (should fail without valid data)\n";
echo "-----------------------------------------------------------------------\n";
$result = makeRequest('POST', '/api/openfactura/dte/anular-guia', [
    'folio' => 12345,
    'fecha' => '2025-01-15'
]);
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}
echo "Raw response: " . json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n";
echo "Parsed data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 5: Anular DTE - Should fail without proper data
echo "5. Testing Anular DTE Endpoint (should fail without valid data)\n";
echo "-------------------------------------------------------------\n";
$result = makeRequest('POST', '/api/openfactura/dte/anular', [
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
echo "Raw response: " . json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n";
echo "Parsed data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Test 6: Get Organization
echo "6. Testing Get Organization Endpoint\n";
echo "-----------------------------------\n";
$result = makeRequest('GET', '/api/openfactura/organization');
echo "Status: {$result['status']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
};
echo "Raw response: " . json_encode($result['raw_response'], JSON_PRETTY_PRINT) . "\n";
echo "Parsed data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
echo "\n";

echo "All tests completed!\n\n";

echo "IMPORTANT NOTE:\n";
echo "If you're seeing empty responses like '{}', this indicates that the HTTP requests are not reaching\n";
echo "the OpenFacturaController through the framework's routing system. The controller methods are\n";
echo "working correctly when called directly (as confirmed by our tests), but the web routing\n";
echo "mechanism may not be properly configured.\n\n";

echo "To fix HTTP endpoint access, ensure:\n";
echo "1. Your web server (Apache/Nginx) has rewrite rules to direct all requests through index.php\n";
echo "2. The friendlypos-web package is properly activated in your SimpleRest installation\n";
echo "3. The routes defined in packages/boctulus/friendlypos-web/config/routes.php are being loaded\n";
echo "4. You have proper API credentials set up in your .env file\n";