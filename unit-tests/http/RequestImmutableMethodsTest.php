<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Request;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

require_once __DIR__ . '/../app.php';

/**
 * Pruebas unitarias para métodos inmutables de Request (Fase 2)
 *
 * Ejecutar con: `./vendor/bin/phpunit tests/RequestImmutableMethodsTest.php`
 */
class RequestImmutableMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset singleton
        Request::setInstance(null);
    }

    protected function tearDown(): void
    {
        // Clean up
        Request::setInstance(null);
        parent::tearDown();
    }

    /**
     * Test withQueryParam() creates new instance
     */
    public function testWithQueryParamCreatesNewInstance()
    {
        $request = Request::getInstance();
        $modified = $request->withQueryParam('key', 'value');

        // Should return a different instance
        $this->assertNotSame($request, $modified);
        $this->assertInstanceOf(Request::class, $modified);
    }

    /**
     * Test withQueryParam() adds parameter
     */
    public function testWithQueryParamAddsParameter()
    {
        $request = Request::getInstance();
        $modified = $request->withQueryParam('test', 'value123');

        // Modified instance should have the new param
        $this->assertEquals('value123', $modified->get('test'));
    }

    /**
     * Test withQueryParam() doesn't modify original
     */
    public function testWithQueryParamDoesntModifyOriginal()
    {
        $request = Request::getInstance();

        // Store original query
        $originalQuery = $request->getQuery();

        $modified = $request->withQueryParam('new_key', 'new_value');

        // Original should remain unchanged
        $this->assertEquals($originalQuery, $request->getQuery());
    }

    /**
     * Test withoutQueryParam() removes parameter
     */
    public function testWithoutQueryParamRemovesParameter()
    {
        $request = Request::getInstance();
        $withParam = $request->withQueryParam('remove_me', 'value');
        $withoutParam = $withParam->withoutQueryParam('remove_me');

        $this->assertNull($withoutParam->get('remove_me'));
        $this->assertNotSame($withParam, $withoutParam);
    }

    /**
     * Test withHeader() creates new instance
     */
    public function testWithHeaderCreatesNewInstance()
    {
        $request = Request::getInstance();
        $modified = $request->withHeader('X-Custom-Header', 'value');

        $this->assertNotSame($request, $modified);
        $this->assertInstanceOf(Request::class, $modified);
    }

    /**
     * Test withHeader() adds header
     */
    public function testWithHeaderAddsHeader()
    {
        $request = Request::getInstance();
        $modified = $request->withHeader('X-Test-Header', 'test-value');

        $this->assertEquals('test-value', $modified->getHeader('X-Test-Header'));
    }

    /**
     * Test withHeader() is case-insensitive
     */
    public function testWithHeaderCaseInsensitive()
    {
        $request = Request::getInstance();
        $modified = $request->withHeader('X-Custom-Header', 'value');

        $this->assertEquals('value', $modified->getHeader('x-custom-header'));
        $this->assertEquals('value', $modified->getHeader('X-CUSTOM-HEADER'));
    }

    /**
     * Test withoutHeader() removes header
     */
    public function testWithoutHeaderRemovesHeader()
    {
        $request = Request::getInstance();
        $withHeader = $request->withHeader('X-Remove-Me', 'value');
        $withoutHeader = $withHeader->withoutHeader('X-Remove-Me');

        $this->assertNull($withoutHeader->getHeader('X-Remove-Me'));
    }

    /**
     * Test withBody() creates new instance
     */
    public function testWithBodyCreatesNewInstance()
    {
        $request = Request::getInstance();
        $modified = $request->withBody(['key' => 'value']);

        $this->assertNotSame($request, $modified);
        $this->assertInstanceOf(Request::class, $modified);
    }

    /**
     * Test withBody() sets body
     */
    public function testWithBodySetsBody()
    {
        $request = Request::getInstance();
        $bodyData = ['test' => 'data', 'number' => 42];
        $modified = $request->withBody($bodyData);

        $body = $modified->getBody(false); // false = as array
        $this->assertEquals($bodyData, $body);
    }

    /**
     * Test chaining immutable methods
     */
    public function testChainingImmutableMethods()
    {
        $request = Request::getInstance();

        $modified = $request
            ->withQueryParam('param1', 'value1')
            ->withQueryParam('param2', 'value2')
            ->withHeader('X-Custom', 'header-value')
            ->withBody(['data' => 'test']);

        // All modifications should be present
        $this->assertEquals('value1', $modified->get('param1'));
        $this->assertEquals('value2', $modified->get('param2'));
        $this->assertEquals('header-value', $modified->getHeader('X-Custom'));
        $this->assertEquals(['data' => 'test'], $modified->getBody(false));

        // Original should be unchanged
        $this->assertNull($request->get('param1'));
    }

    /**
     * Test multiple instances remain independent
     */
    public function testMultipleInstancesAreIndependent()
    {
        $request1 = Request::getInstance();
        $request2 = $request1->withQueryParam('key1', 'value1');
        $request3 = $request1->withQueryParam('key2', 'value2');

        // request2 and request3 should be different
        $this->assertNotSame($request2, $request3);

        // Each should have only its own modifications
        $this->assertEquals('value1', $request2->get('key1'));
        $this->assertNull($request2->get('key2'));

        $this->assertEquals('value2', $request3->get('key2'));
        $this->assertNull($request3->get('key1'));
    }
}
