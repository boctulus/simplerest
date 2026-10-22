<?php

namespace Boctulus\Simplerest\Tests\Http;

use Boctulus\Simplerest\Core\Libs\CorsHandler;
use PHPUnit\Framework\TestCase;

class CorsHandlerOriginTest extends TestCase
{
    private array $originalServer;
    private $originalStatusCode;

    protected function setUp(): void
    {
        $this->originalServer = $_SERVER;
        $this->originalStatusCode = http_response_code();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;

        if (is_int($this->originalStatusCode)) {
            http_response_code($this->originalStatusCode);
        }
    }

    public function test_referer_does_not_substitute_for_a_missing_origin(): void
    {
        $_SERVER = array_merge($this->originalServer, [
            'REQUEST_URI' => '/api/test',
            'REQUEST_METHOD' => 'GET',
            'HTTP_REFERER' => 'https://untrusted.example/private/page',
        ]);
        unset($_SERVER['HTTP_ORIGIN']);
        http_response_code(200);

        $headersBefore = headers_list();
        $handler = new CorsHandler(['*'], ['https://trusted.example']);
        $handler->handle();

        $this->assertSame(200, http_response_code());
        $this->assertSame($headersBefore, headers_list());
    }

    public function test_disallowed_origin_remains_forbidden(): void
    {
        $_SERVER = array_merge($this->originalServer, [
            'REQUEST_URI' => '/api/test',
            'REQUEST_METHOD' => 'GET',
            'HTTP_ORIGIN' => 'https://untrusted.example',
        ]);

        $handler = new CorsHandler(['*'], ['https://trusted.example']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(403);

        $handler->handle();
    }
}
