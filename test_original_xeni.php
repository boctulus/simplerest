<?php
// Test script for original Xeni API test endpoint

echo "Testing original Xeni API test endpoint...\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/simplerest/xeni/test");
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
    echo "\n✓ Original endpoint is working correctly!\n";
} else {
    echo "\n✗ Original endpoint returned an error.\n";
}