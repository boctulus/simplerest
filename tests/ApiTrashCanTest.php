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
use Boctulus\Simplerest\Core\Libs\ApiClient;

define('HOST', parse_url($config['app_url'], PHP_URL_HOST));
define('BASE_URL', rtrim($config['app_url'], '/') . '/');

/*
    * Requiere PHPUnit y una configuración adecuada de la base de datos.
    *
    * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ApiTrashCanTest.php    
    *
*/

/**
 * API Trash Can Test Cases
 */
class ApiTrashCanTest extends TestCase
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
     * Test soft deleting a product and checking it appears in trash can
     */
    function testSoftDeleteAndTrashCan()
    {
        // Create a test product directly in soft-deleted state
        $name = 'Test Product for Trash Can ' . uniqid();
        $productId = DB::table('products')->create([
            'name' => $name,
            'description' => 'Test product description for trash can',
            'cost' => 10.99,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'images' => '[]',
            'belongs_to' => $this->uid,
            'deleted_at' => date('Y-m-d H:i:s') // Create already soft-deleted
        ]);

        $this->assertIsNumeric($productId);

        $client = new ApiClient();

        // Now check if the deleted product appears in the trash can
        $res2 = $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->decode()
            ->get(BASE_URL . 'api/v1/trash_can?entity=Products')
            ->getDataOrFail();

        if (!isset($res2['data'])) {
            $this->fail('trash_can response does not have "data" key. Response: ' . json_encode($res2));
        }

        $this->assertArrayHasKey('data', $res2);

        // Find our deleted product in the trash can results
        $foundInTrash = false;
        if (is_array($res2['data'])) {
            foreach ($res2['data'] as $item) {
                if (isset($item['id']) && $item['id'] == $productId) {
                    $foundInTrash = true;
                    break;
                }
            }
        }

        $this->assertTrue($foundInTrash, "Product was not found in trash can after soft delete");

        // Clean up: permanently delete the item from trash
        $deleteFromTrashData = [
            'entity' => 'products'
        ];

        $res3 = $client
            ->addHeader('Authorization', "Bearer {$this->at}")
            ->addHeader('Content-Type', 'application/json')
            ->setBody($deleteFromTrashData)
            ->decode()
            ->request(BASE_URL . "api/v1/trash_can/$productId", 'DELETE')
            ->getDataOrFail();

        $this->assertArrayHasKey('success', $res3);
        $this->assertTrue($res3['success']);
    }

    /*
     * Test getting a specific item from trash can
     */
    function testGetSpecificItemFromTrashCan()
    {
        // First, create a test product
        $name = 'Test Product for Trash Can Specific ' . uniqid();
        $productId = DB::table('products')->create([
            'name' => $name,
            'description' => 'Test product description for trash can specific',
            'cost' => 15.99,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'images' => '[]', // Default empty array as JSON string
            'belongs_to' => $this->uid
        ]);

        $this->assertIsNumeric($productId);

        // Soft delete the product
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$productId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            throw new \Exception("$error_msg ($http_code)");
        }

        $this->assertEquals(200, $http_code);

        curl_close($ch);

        // Now get the specific item from trash can
        $ch2 = curl_init();

        curl_setopt_array($ch2, array(
            CURLOPT_URL => BASE_URL . "api/v1/trash_can/$productId?entity=products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch2, CURLOPT_FAILONERROR, true);

        $response2 = curl_exec($ch2);
        $err2 = curl_error($ch2);
        $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

        if (curl_errno($ch2)) {
            $error_msg2 = curl_error($ch2);
            throw new \Exception("$error_msg2 ($http_code2)");
        }

        $res2 = json_decode($response2, true);

        $this->assertEquals(200, $http_code2);
        $this->assertArrayHasKey('data', $res2);
        $this->assertEquals($productId, $res2['data']['id']);

        curl_close($ch2);

        // Clean up: permanently delete the item from trash
        $ch3 = curl_init();

        $deleteFromTrashData = json_encode([
            'entity' => 'products'
        ]);

        curl_setopt_array($ch3, array(
            CURLOPT_URL => BASE_URL . "api/v1/trash_can/$productId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $deleteFromTrashData,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch3, CURLOPT_FAILONERROR, true);

        $response3 = curl_exec($ch3);
        $err3 = curl_error($ch3);
        $http_code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);

        if (curl_errno($ch3)) {
            $error_msg3 = curl_error($ch3);
            throw new \Exception("$error_msg3 ($http_code3)");
        }

        $res3 = json_decode($response3, true);

        $this->assertEquals(200, $http_code3);
        $this->assertTrue($res3['success']);

        curl_close($ch3);
    }

    /*
     * Test undeleting an item from trash can
     */
    function testUndeleteFromTrashCan()
    {
        // First, create a test product
        $name = 'Test Product for Undelete ' . uniqid();
        $productId = DB::table('products')->create([
            'name' => $name,
            'description' => 'Test product description for undelete',
            'cost' => 20.99,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'images' => '[]', // Default empty array as JSON string
            'belongs_to' => $this->uid
        ]);

        $this->assertIsNumeric($productId);

        // Soft delete the product
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$productId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            throw new \Exception("$error_msg ($http_code)");
        }

        $this->assertEquals(200, $http_code);

        curl_close($ch);

        // Verify the product is in the trash can
        $ch2 = curl_init();

        curl_setopt_array($ch2, array(
            CURLOPT_URL => BASE_URL . "api/v1/trash_can/$productId?entity=products",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch2, CURLOPT_FAILONERROR, true);

        $response2 = curl_exec($ch2);
        $err2 = curl_error($ch2);
        $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

        if (curl_errno($ch2)) {
            $error_msg2 = curl_error($ch2);
            throw new \Exception("$error_msg2 ($http_code2)");
        }

        $res2 = json_decode($response2, true);

        $this->assertEquals(200, $http_code2);
        $this->assertArrayHasKey('data', $res2);
        $this->assertEquals($productId, $res2['data']['id']);

        curl_close($ch2);

        // Now undelete the product using PATCH on trash_can
        $ch3 = curl_init();

        $undeleteData = json_encode([
            'entity' => 'Products',
            'trashed' => false
        ]);

        curl_setopt_array($ch3, array(
            CURLOPT_URL => BASE_URL . "api/v1/trash_can/$productId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => $undeleteData,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch3, CURLOPT_FAILONERROR, true);

        $response3 = curl_exec($ch3);
        $err3 = curl_error($ch3);
        $http_code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);

        if (curl_errno($ch3)) {
            $error_msg3 = curl_error($ch3);
            throw new \Exception("$error_msg3 ($http_code3)");
        }

        $res3 = json_decode($response3, true);

        $this->assertEquals(200, $http_code3);
        $this->assertTrue($res3['success']);

        curl_close($ch3);

        // Verify the product is back in the products list (not in trash anymore)
        $ch4 = curl_init();

        curl_setopt_array($ch4, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$productId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch4, CURLOPT_FAILONERROR, true);

        $response4 = curl_exec($ch4);
        $err4 = curl_error($ch4);
        $http_code4 = curl_getinfo($ch4, CURLINFO_HTTP_CODE);

        if (curl_errno($ch4)) {
            $error_msg4 = curl_error($ch4);
            throw new \Exception("$error_msg4 ($http_code4)");
        }

        $res4 = json_decode($response4, true);

        $this->assertEquals(200, $http_code4);
        $this->assertArrayHasKey('data', $res4);
        $this->assertEquals($productId, $res4['data']['id']);

        curl_close($ch4);

        // Clean up: delete the product for real
        $ch5 = curl_init();

        curl_setopt_array($ch5, array(
            CURLOPT_URL => BASE_URL . "api/v1/products/$productId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch5, CURLOPT_FAILONERROR, true);

        $response5 = curl_exec($ch5);
        $err5 = curl_error($ch5);
        $http_code5 = curl_getinfo($ch5, CURLINFO_HTTP_CODE);

        if (curl_errno($ch5)) {
            $error_msg5 = curl_error($ch5);
            throw new \Exception("$error_msg5 ($http_code5)");
        }

        $res5 = json_decode($response5, true);

        $this->assertEquals(200, $http_code5);
        $this->assertTrue($res5['success']);

        curl_close($ch5);
    }

    /*
     * Test trash can with invalid entity
     */
    function testTrashCanWithInvalidEntity()
    {
        // This test checks that trash_can returns proper error for invalid entity
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => BASE_URL . "api/v1/trash_can?entity=nonexistent_entity",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Authorization: Bearer $this->at",
                "Content-Type: application/json",
                "Host: " . HOST,
                "cache-control: no-cache"
            ),
        ));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Should return 404 or 501 since the entity doesn't exist
        $this->assertTrue($http_code == 404 || $http_code == 501);

        curl_close($ch);
    }
}