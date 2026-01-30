<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app.php';

use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\DB;

$config = \Boctulus\Simplerest\Core\Libs\Config::get();
define('BASE_URL', rtrim($config['app_url'], '/') . '/');

// Login
$client = new ApiClient();
$login_data = ['email' => 'tester3@g.c', 'password' => 'gogogo'];

$res = $client
    ->addHeader('Content-Type', 'application/json')
    ->setBody($login_data)
    ->decode()
    ->post(BASE_URL . 'api/v1/auth/login')
    ->getDataOrFail();

$token = $res['data']['access_token'];
$uid = $res['data']['uid'];

echo "Logged in as UID: $uid\n";
echo "Token: " . substr($token, 0, 50) . "...\n\n";

// Create test products
$test_names = ['DebugProdA', 'DebugProdB', 'DebugProdC'];
$created_ids = [];

foreach ($test_names as $name) {
    $product_data = [
        'name' => $name,
        'cost' => 100,
        'description' => 'Debug test',
        'slug' => strtolower($name) . '-' . time(),
        'images' => '[]'
    ];

    $res = $client
        ->addHeader('Authorization', "Bearer $token")
        ->addHeader('Content-Type', 'application/json')
        ->setBody($product_data)
        ->decode()
        ->post(BASE_URL . 'api/v1/products')
        ->getData();

    if (!isset($res['data']['id'])) {
        echo "Error creating product $name:\n";
        var_dump($res);
        continue;
    }

    $created_ids[] = $res['data']['id'];
    echo "Created product $name with ID: {$res['data']['id']}\n";
}

echo "\n--- Testing IN operator ---\n\n";

// Test 1: Explicit IN with [in]
echo "Test 1: Explicit IN ?name[in]=DebugProdA,DebugProdB\n";
$res1 = $client
    ->addHeader('Authorization', "Bearer $token")
    ->decode()
    ->get(BASE_URL . 'api/v1/products?name[in]=DebugProdA,DebugProdB&limit=100')
    ->getDataOrFail();

echo "Found " . count($res1['data']) . " products\n";
foreach ($res1['data'] as $p) {
    if (in_array($p['name'], $test_names)) {
        echo "  - {$p['id']}: {$p['name']}\n";
    }
}

// Test 2: Auto-detected IN (comma-separated)
echo "\nTest 2: Auto-detected IN ?name=DebugProdA,DebugProdB\n";
$res2 = $client
    ->addHeader('Authorization', "Bearer $token")
    ->decode()
    ->get(BASE_URL . 'api/v1/products?name=DebugProdA,DebugProdB&limit=100')
    ->getDataOrFail();

echo "Found " . count($res2['data']) . " products\n";
foreach ($res2['data'] as $p) {
    if (in_array($p['name'], $test_names)) {
        echo "  - {$p['id']}: {$p['name']}\n";
    }
}

// Cleanup
echo "\n--- Cleanup ---\n";
foreach ($created_ids as $id) {
    $client
        ->addHeader('Authorization', "Bearer $token")
        ->request(BASE_URL . "api/v1/products/$id", 'DELETE');
    echo "Deleted product ID: $id\n";
}

echo "\nDone!\n";
