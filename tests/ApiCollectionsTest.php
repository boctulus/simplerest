<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() != "cli"){
    return;
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Validator;
use Boctulus\Simplerest\Core\Libs\ApiClient;

define('HOST', parse_url($config['app_url'], PHP_URL_HOST));
define('BASE_URL', rtrim($config['app_url'], '/') . '/');

/*
    * Requiere PHPUnit y una configuración adecuada de la base de datos.
    *
    * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ApiCollectionsTest.php    
    *
*/

/**
 * API Collections Test Cases
 */
class ApiCollectionsTest extends TestCase
{
    private $uid;
    private $at;
    private $rt;
    protected $config;

    private function login($credentials){
        $client = new ApiClient();

        $response = $client
            ->setBody($credentials)
            ->decode()
            ->addHeader('Content-Type', 'application/json')
            ->post(BASE_URL . 'api/v1/auth/login')
            ->getDataOrFail();

        if (isset($response['error']) && !empty($response['error'])) {
            $errorMsg = is_array($response['error']) ? json_encode($response['error']) : $response['error'];
            throw new \Exception($errorMsg);
        }

        $access_token = $response['data']['access_token'];
        $refresh_token = $response['data']['refresh_token'];

        return [$access_token, $refresh_token];
    }

    private function get_me(string $at){
        $client = new ApiClient();

        $response = $client
            ->addHeader('Authorization', "Bearer $at")
            ->decode()
            ->get(BASE_URL . 'api/v1/me')
            ->getDataOrFail();

        if (!isset($response['data']['id']) || !isset($response['data']['email']))
            throw new \Exception("Empty uid or email");

        $data = $response['data'];

        // [id, username, emai,... ]
        return $data;
    }

    protected function setUp(): void {
        parent::setUp();
        $this->config = \Boctulus\Simplerest\Core\Libs\Config::get();

        list($this->at, $this->rt) = $this->login(['email' => "tester3@g.c", "password" => "gogogo"]);
        $this->uid = $this->get_me($this->at)['id'];
    }

    /*
     * Test creating a collection
     */
    function testCreateCollection()
    {
        // First, get some products to use in the collection
        $products = DB::table('products')->where(['belongs_to' => $this->uid])->limit(3)->get();

        if (count($products) < 2) {
            // Create some test products if we don't have enough
            for ($i = 0; $i < 3; $i++) {
                $name = 'Test Product for Collection ' . uniqid();
                $productId = DB::table('products')->create([
                    'name' => $name,
                    'description' => 'Test product description',
                    'cost' => 10.99,
                    'slug' => strtolower(str_replace(' ', '-', $name)),
                    'images' => '[]', // Required field
                    'belongs_to' => $this->uid
                ]);

                if ($productId) {
                    $products[] = ['id' => $productId];
                }
            }
        }

        if (count($products) < 2) {
            $this->markTestSkipped('Need at least 2 products to test collections');
        }

        $productIds = array_column($products, 'id');

        $collectionData = [
            'entity' => 'products',
            'refs' => $productIds
        ];

        $client = new ApiClient();

        $res = $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->addHeader('Content-Type', 'application/json')
            ->setBody($collectionData)
            ->decode()
            ->post(BASE_URL . 'api/v1/collections')
            ->getDataOrFail();

        // Debug
        if (!isset($res['data'])) {
            $this->fail('Response does not have "data" key. Response: ' . json_encode($res));
        }

        $this->assertArrayHasKey('data', $res);
        $this->assertArrayHasKey('id', $res['data']);
        $this->assertIsNumeric($res['data']['id']);

        // Clean up: delete the collection
        $collectionId = $res['data']['id'];
        $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->decode()
            ->delete(BASE_URL . "api/v1/collections/$collectionId?entity=products");
    }

    /*
     * Test creating a collection with invalid entity
     */
    function testCreateCollectionWithInvalidEntity()
    {
        $client = new ApiClient();

        $collectionData = [
            'entity' => 'nonexistent_entity',
            'refs' => [1, 2, 3]
        ];

        $res = $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->addHeader('Content-Type', 'application/json')
            ->setBody($collectionData)
            ->decode()
            ->post(BASE_URL . 'api/v1/collections')
            ->getDataOrFail();

        $this->assertArrayHasKey('status', $res);
        $this->assertEquals(400, $res['status']);
    }

    /*
     * Test creating a collection with forbidden table
     */
    function testCreateCollectionWithForbiddenTable()
    {
        $client = new ApiClient();

        $collectionData = [
            'entity' => 'users',  // This is in the forbidden list
            'refs' => [1, 2, 3]
        ];

        $res = $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->addHeader('Content-Type', 'application/json')
            ->setBody($collectionData)
            ->decode()
            ->post(BASE_URL . 'api/v1/collections')
            ->getDataOrFail();

        $this->assertArrayHasKey('status', $res);
        $this->assertEquals(403, $res['status']);  // Forbidden
    }

    /*
     * Test getting collections
     */
    function testGetCollections()
    {
        $client = new ApiClient();

        $res = $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->decode()
            ->get(BASE_URL . 'api/v1/collections?entity=products')
            ->getDataOrFail();

        $this->assertArrayHasKey('data', $res);
    }
}