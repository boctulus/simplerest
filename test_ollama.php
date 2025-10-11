<?php

require __DIR__ . '/vendor/autoload.php';

use Boctulus\Simplerest\Core\Libs\ApiClient;

$client = ApiClient::instance()
    ->setBaseUrl('http://localhost:11434')
    ->setHeaders([
        'Content-Type' => 'application/json'
    ])
    ->disableSSL()
    ->decode();

$client->get('/api/tags');

$resp = $client->getResponse();

echo "Response type: " . gettype($resp) . "\n";
echo "Response:\n";
print_r($resp);
echo "\n\nData only:\n";
print_r($client->data());
