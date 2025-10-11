<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\ApiClient;

/*
    Tests for the ApiClient class.

    This class tests the functionality of the ApiClient including mocking responses,
    decoding JSON data, and checking request properties.   
    
    Ejecución de tests:
    vendor\bin\phpunit packages\boctulus\dummyapi\tests\ApiClientTest.php
*/

class ApiClientTest extends TestCase
{
    public function test_mocking_works_with_200_status()
    {
        $client = new ApiClient();
        $mockData = ['status' => 'ok'];

        // Simula una respuesta exitosa
        $client->mock($mockData, 200)->decode()->get('http://any-url.com');

        $this->assertEquals(200, $client->status());
        $this->assertEquals($mockData, $client->data());
    }

    public function test_mocking_works_with_error_status()
    {
        $client = new ApiClient();
        $mockData = ['error' => 'Not Found'];

        // Simula una respuesta de error 404
        $client->mock($mockData, 404)->decode()->get('http://any-url.com');

        $this->assertEquals(404, $client->status());
        $this->assertEquals($mockData, $client->data());
    }

    public function test_data_is_correctly_decoded()
    {
        $client = new ApiClient();
        $jsonString = '{"user_id": 123, "active": true}';
        $expectedArray = ['user_id' => 123, 'active' => true];

        // Simula una respuesta en formato JSON
        $client->mock($jsonString, 200)->decode()->get('http://any-url.com');

        $this->assertEquals(200, $client->status());
        $this->assertEquals($expectedArray, $client->data());
    }

    public function test_dump_method_reflects_request_properties()
    {
        $client = new ApiClient();
        $postData = ['name' => 'John'];
        $headers = ['X-Test-Header' => 'value'];

        $client->setUrl('http://test-url.com/api')
               ->setBody($postData)
               ->setHeaders($headers)
               ->setRetries(3);

        $dump = $client->dump();

        $this->assertEquals('http://test-url.com/api', $dump['url']);
        $this->assertEquals($postData, $dump['body']);
        $this->assertEquals($headers, $dump['headers']);
        $this->assertEquals(3, $dump['max_retries']);
    }
    
    public function test_get_response_method_structure()
    {
        $client = new ApiClient();
        $mockData = ['user' => 'test'];

        $client->mock($mockData, 201)->decode()->get('http://any-url.com');

        $response = $client->getResponse();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('http_code', $response);
        $this->assertArrayHasKey('error', $response);

        $this->assertEquals($mockData, $response['data']);
        $this->assertEquals(201, $response['http_code']);
        $this->assertNull($response['error']);
    }
}