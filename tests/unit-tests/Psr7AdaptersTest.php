<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Psr7\StreamAdapter;
use Boctulus\Simplerest\Core\Psr7\UriAdapter;
use Boctulus\Simplerest\Core\Psr7\ServerRequestAdapter;
use Boctulus\Simplerest\Core\Psr7\ResponseAdapter;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

require_once __DIR__ . '/../../app.php';

/**
 * Prueba unitaria para PSR-7 Adapters
 *
 * Ejecutar con: `./vendor/bin/phpunit tests/unit-tests/Psr7AdaptersTest.php`
 */
class Psr7AdaptersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Reset singleton instances
        Request::setInstance(null);
        Response::setInstance(null);
    }

    protected function tearDown(): void
    {
        // Clean up singleton instances
        Request::setInstance(null);
        Response::setInstance(null);

        parent::tearDown();
    }

    /**
     * Test StreamAdapter basic functionality
     */
    public function testStreamAdapterWithString()
    {
        $content = 'Hello, PSR-7!';
        $stream = new StreamAdapter($content);

        $this->assertInstanceOf(\Psr\Http\Message\StreamInterface::class, $stream);
        $this->assertEquals($content, (string) $stream);
        $this->assertEquals(strlen($content), $stream->getSize());
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isSeekable());
    }

    /**
     * Test StreamAdapter with array (converts to JSON)
     */
    public function testStreamAdapterWithArray()
    {
        $data = ['key' => 'value', 'number' => 42];
        $stream = new StreamAdapter($data);

        $expectedJson = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expectedJson, (string) $stream);
    }

    /**
     * Test StreamAdapter read/write operations
     */
    public function testStreamAdapterReadWrite()
    {
        $stream = new StreamAdapter('');

        $written = $stream->write('Test content');
        $this->assertEquals(12, $written);

        $stream->rewind();
        $content = $stream->read(4);
        $this->assertEquals('Test', $content);
    }

    /**
     * Test UriAdapter basic parsing
     */
    public function testUriAdapterBasicParsing()
    {
        $uriString = 'https://user:pass@example.com:8080/path/to/resource?key=value#fragment';
        $uri = new UriAdapter($uriString);

        $this->assertInstanceOf(\Psr\Http\Message\UriInterface::class, $uri);
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path/to/resource', $uri->getPath());
        $this->assertEquals('key=value', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());
    }

    /**
     * Test UriAdapter immutability (with methods)
     */
    public function testUriAdapterImmutability()
    {
        $original = new UriAdapter('https://example.com/path');
        $modified = $original->withHost('newhost.com');

        $this->assertNotSame($original, $modified);
        $this->assertEquals('example.com', $original->getHost());
        $this->assertEquals('newhost.com', $modified->getHost());
    }

    /**
     * Test UriAdapter string reconstruction
     */
    public function testUriAdapterToString()
    {
        $uriString = 'https://example.com:8080/path?query=value#fragment';
        $uri = new UriAdapter($uriString);

        $this->assertEquals($uriString, (string) $uri);
    }

    /**
     * Test ServerRequestAdapter creation
     */
    public function testServerRequestAdapterCreation()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())->method('getQuery')->willReturn(['key' => 'value']);
        $mockRequest->expects($this->any())->method('getBody')->willReturn(['data' => 'test']);
        $mockRequest->expects($this->any())->method('headers')->willReturn(['content-type' => 'application/json']);
        $mockRequest->expects($this->any())->method('method')->willReturn('GET');

        $adapter = new ServerRequestAdapter($mockRequest);

        $this->assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $adapter);
        $this->assertEquals('GET', $adapter->getMethod());
        $this->assertEquals(['key' => 'value'], $adapter->getQueryParams());
    }

    /**
     * Test ServerRequestAdapter immutability
     */
    public function testServerRequestAdapterImmutability()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())->method('getQuery')->willReturn([]);
        $mockRequest->expects($this->any())->method('headers')->willReturn([]);

        $original = new ServerRequestAdapter($mockRequest);
        $modified = $original->withQueryParams(['new' => 'value']);

        // Verify immutability - modified is a different instance
        $this->assertNotSame($original, $modified);

        // Original should still return empty array (from mock)
        $this->assertEquals([], $original->getQueryParams());

        // Note: Due to how the adapter works with reflection and cloning,
        // the modified instance will have the new query params
        // This verifies the with* pattern creates a new instance
        $this->assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $modified);
    }

    /**
     * Test ServerRequestAdapter attributes
     */
    public function testServerRequestAdapterAttributes()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())->method('headers')->willReturn([]);

        $adapter = new ServerRequestAdapter($mockRequest);

        $this->assertEquals([], $adapter->getAttributes());
        $this->assertNull($adapter->getAttribute('missing'));
        $this->assertEquals('default', $adapter->getAttribute('missing', 'default'));

        $modified = $adapter->withAttribute('key', 'value');
        $this->assertEquals('value', $modified->getAttribute('key'));

        $removed = $modified->withoutAttribute('key');
        $this->assertNull($removed->getAttribute('key'));
    }

    /**
     * Test ResponseAdapter creation
     */
    public function testResponseAdapterCreation()
    {
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('get')->willReturn('response data');

        $adapter = new ResponseAdapter($mockResponse);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $adapter);
        $this->assertEquals(200, $adapter->getStatusCode());
        $this->assertEquals('OK', $adapter->getReasonPhrase());
    }

    /**
     * Test ResponseAdapter status code
     */
    public function testResponseAdapterStatusCode()
    {
        $adapter = new ResponseAdapter();

        $modified = $adapter->withStatus(404, 'Not Found');
        $this->assertEquals(404, $modified->getStatusCode());
        $this->assertEquals('Not Found', $modified->getReasonPhrase());

        // Test default reason phrase
        $modified2 = $adapter->withStatus(500);
        $this->assertEquals('Internal Server Error', $modified2->getReasonPhrase());
    }

    /**
     * Test ResponseAdapter headers
     */
    public function testResponseAdapterHeaders()
    {
        $adapter = new ResponseAdapter();

        $this->assertFalse($adapter->hasHeader('X-Custom'));

        $modified = $adapter->withHeader('X-Custom', 'value');
        $this->assertTrue($modified->hasHeader('X-Custom'));
        $this->assertEquals(['value'], $modified->getHeader('X-Custom'));
        $this->assertEquals('value', $modified->getHeaderLine('X-Custom'));

        $added = $modified->withAddedHeader('X-Custom', 'value2');
        $this->assertEquals(['value', 'value2'], $added->getHeader('X-Custom'));

        $removed = $added->withoutHeader('X-Custom');
        $this->assertFalse($removed->hasHeader('X-Custom'));
    }

    /**
     * Test ResponseAdapter body
     */
    public function testResponseAdapterBody()
    {
        $adapter = new ResponseAdapter();

        $body = new StreamAdapter('Test body content');
        $modified = $adapter->withBody($body);

        $this->assertEquals('Test body content', (string) $modified->getBody());
    }

    /**
     * Test ResponseAdapter JSON helper
     */
    public function testResponseAdapterJsonHelper()
    {
        $adapter = new ResponseAdapter();

        $data = ['success' => true, 'message' => 'OK'];
        $jsonResponse = $adapter->withJson($data, 201);

        $this->assertEquals(201, $jsonResponse->getStatusCode());
        $this->assertEquals(['application/json'], $jsonResponse->getHeader('Content-Type'));

        $expectedJson = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expectedJson, (string) $jsonResponse->getBody());
    }

    /**
     * Test PSR-7 helper functions
     */
    public function testPsr7HelperFunctions()
    {
        // Test psr7_stream()
        $stream = psr7_stream('test content');
        $this->assertInstanceOf(\Psr\Http\Message\StreamInterface::class, $stream);
        $this->assertEquals('test content', (string) $stream);

        // Test psr7_uri()
        $uri = psr7_uri('https://example.com/path');
        $this->assertInstanceOf(\Psr\Http\Message\UriInterface::class, $uri);
        $this->assertEquals('example.com', $uri->getHost());

        // Test psr7_json()
        $jsonResponse = psr7_json(['key' => 'value'], 200);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $jsonResponse);
        $this->assertEquals(200, $jsonResponse->getStatusCode());

        // Test psr7_redirect()
        $redirect = psr7_redirect('https://example.com', 302);
        $this->assertEquals(302, $redirect->getStatusCode());
        $this->assertEquals(['https://example.com'], $redirect->getHeader('Location'));

        // Test psr7_html()
        $html = psr7_html('<h1>Title</h1>', 200);
        $this->assertEquals(['text/html; charset=utf-8'], $html->getHeader('Content-Type'));

        // Test psr7_text()
        $text = psr7_text('Plain text', 200);
        $this->assertEquals(['text/plain; charset=utf-8'], $text->getHeader('Content-Type'));
    }
}
