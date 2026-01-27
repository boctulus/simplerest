<?php
// Test script for Xeni API v1 authentication
// This script can be used to test the new /xeni/v1/test endpoint

echo "Testing Xeni API v1 authentication endpoint...\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/simplerest/xeni/v1/test");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Only for testing

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: " . $http_code . "\n";
echo "Response:\n";
echo $response . "\n";

if ($http_code == 200) {
    echo "\n✓ Endpoint is working correctly!\n";
} else {
    echo "\n✗ Endpoint returned an error.\n";
}