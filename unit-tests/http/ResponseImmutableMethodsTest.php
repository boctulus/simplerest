<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Response;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

require_once __DIR__ . '/../app.php';

/**
 * Pruebas unitarias para métodos inmutables de Response (Fase 2)
 *
 * Ejecutar con: `./vendor/bin/phpunit tests/ResponseImmutableMethodsTest.php`
 */
class ResponseImmutableMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset singleton
        Response::setInstance(null);
    }

    protected function tearDown(): void
    {
        // Clean up
        Response::setInstance(null);
        parent::tearDown();
    }

    /**
     * Test withStatus() creates new instance
     */
    public function testWithStatusCreatesNewInstance()
    {
        $response = Response::getInstance();
        $modified = $response->withStatus(404);

        $this->assertNotSame($response, $modified);
        $this->assertInstanceOf(Response::class, $modified);
    }

    /**
     * Test withStatus() sets status code
     */
    public function testWithStatusSetsStatusCode()
    {
        $response = Response::getInstance();
        $modified = $response->withStatus(201);

        // Verify status was set (we'll check through reflection)
        $reflection = new \ReflectionClass($modified);
        $property = $reflection->getProperty('http_code');
        $property->setAccessible(true);

        $this->assertEquals(201, $property->getValue($modified));
    }

    /**
     * Test withStatus() with custom reason phrase
     */
    public function testWithStatusWithReasonPhrase()
    {
        $response = Response::getInstance();
        $modified = $response->withStatus(418, "I'm a teapot");

        $reflection = new \ReflectionClass($modified);
        $property = $reflection->getProperty('http_code_msg');
        $property->setAccessible(true);

        $this->assertEquals("I'm a teapot", $property->getValue($modified));
    }

    /**
     * Test withHeader() creates new instance
     */
    public function testWithHeaderCreatesNewInstance()
    {
        $response = Response::getInstance();
        $modified = $response->withHeader('X-Custom-Header', 'value');

        $this->assertNotSame($response, $modified);
        $this->assertInstanceOf(Response::class, $modified);
    }

    /**
     * Test withHeader() adds header
     */
    public function testWithHeaderAddsHeader()
    {
        $response = Response::getInstance();
        $modified = $response->withHeader('Content-Type', 'application/json');

        // Check header was added (via reflection)
        $reflection = new \ReflectionClass($modified);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($modified);

        $this->assertContains('Content-Type: application/json', $headers);
    }

    /**
     * Test withAddedHeader() appends header value
     */
    public function testWithAddedHeaderAppendsValue()
    {
        $response = Response::getInstance();
        $modified = $response
            ->withHeader('X-Custom', 'value1')
            ->withAddedHeader('X-Custom', 'value2');

        $reflection = new \ReflectionClass($modified);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($modified);

        // Should have multiple headers with X-Custom
        $customHeaders = array_filter($headers, function($header) {
            return strpos($header, 'X-Custom') !== false;
        });

        $this->assertGreaterThanOrEqual(2, count($customHeaders),
            'Should have at least 2 X-Custom headers');
    }

    /**
     * Test withoutHeader() removes header
     */
    public function testWithoutHeaderRemovesHeader()
    {
        $response = Response::getInstance();
        $withHeader = $response->withHeader('X-Remove-Me', 'value');
        $withoutHeader = $withHeader->withoutHeader('X-Remove-Me');

        $reflection = new \ReflectionClass($withoutHeader);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($withoutHeader);

        // Should not contain the removed header
        if (is_array($headers)) {
            foreach ($headers as $header) {
                $this->assertStringNotContainsString('X-Remove-Me', $header);
            }
        }

        // If no headers or header was removed, test passes
        $this->assertTrue(true);
    }

    /**
     * Test withBody() creates new instance
     */
    public function testWithBodyCreatesNewInstance()
    {
        $response = Response::getInstance();
        $modified = $response->withBody(['key' => 'value']);

        $this->assertNotSame($response, $modified);
        $this->assertInstanceOf(Response::class, $modified);
    }

    /**
     * Test withBody() sets data
     */
    public function testWithBodySetsData()
    {
        $response = Response::getInstance();
        $bodyData = ['success' => true, 'message' => 'OK'];
        $modified = $response->withBody($bodyData);

        // Get data via reflection
        $reflection = new \ReflectionClass($modified);
        $property = $reflection->getProperty('data');
        $property->setAccessible(true);

        $this->assertEquals($bodyData, $property->getValue($modified));
    }

    /**
     * Test withJson() convenience method
     */
    public function testWithJsonMethod()
    {
        $response = Response::getInstance();
        $data = ['test' => 'data'];
        $modified = $response->withJson($data, 201);

        // Should set status
        $reflection = new \ReflectionClass($modified);
        $statusProp = $reflection->getProperty('http_code');
        $statusProp->setAccessible(true);
        $this->assertEquals(201, $statusProp->getValue($modified));

        // Should set to_be_encoded flag
        $encodeProp = $reflection->getProperty('to_be_encoded');
        $encodeProp->setAccessible(true);
        $this->assertTrue($encodeProp->getValue($modified));

        // Should set data
        $dataProp = $reflection->getProperty('data');
        $dataProp->setAccessible(true);
        $this->assertEquals($data, $dataProp->getValue($modified));
    }

    /**
     * Test chaining immutable methods
     */
    public function testChainingImmutableMethods()
    {
        $response = Response::getInstance();

        $modified = $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-API-Version', '1.0')
            ->withBody(['success' => true]);

        $this->assertInstanceOf(Response::class, $modified);
        $this->assertNotSame($response, $modified);
    }

    /**
     * Test multiple instances remain independent
     */
    public function testMultipleInstancesAreIndependent()
    {
        $response1 = Response::getInstance();
        $response2 = $response1->withStatus(200);
        $response3 = $response1->withStatus(404);

        // response2 and response3 should be different
        $this->assertNotSame($response2, $response3);

        // Each should have its own status code
        $reflection = new \ReflectionClass($response2);
        $property = $reflection->getProperty('http_code');
        $property->setAccessible(true);
        $this->assertEquals(200, $property->getValue($response2));

        $reflection3 = new \ReflectionClass($response3);
        $property3 = $reflection3->getProperty('http_code');
        $property3->setAccessible(true);
        $this->assertEquals(404, $property3->getValue($response3));
    }
}
