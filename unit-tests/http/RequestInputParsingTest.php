<?php

namespace Boctulus\Simplerest\tests\Http;

use Boctulus\Simplerest\Core\Exceptions\InvalidRequestBodyException;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Request;
use PHPUnit\Framework\TestCase;

class RequestInputParsingTest extends TestCase
{
    private array $originalServer;

    protected function setUp(): void
    {
        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
    }

    public function test_headers_are_normalized_and_include_content_server_variables(): void
    {
        if (function_exists('apache_request_headers')) {
            $this->markTestSkipped('The Apache header source cannot be controlled in this unit test.');
        }

        $_SERVER = [
            'HTTP_ACCEPT_ENCODING' => 'gzip',
            'HTTP_X_REQUEST_ID' => 'request-123',
            'CONTENT_TYPE' => 'application/json; charset=utf-8',
            'CONTENT_LENGTH' => '12',
        ];

        $this->assertSame([
            'accept-encoding' => 'gzip',
            'x-request-id' => 'request-123',
            'content-type' => 'application/json; charset=utf-8',
            'content-length' => '12',
        ], Request::getHeaders());
    }

    public function test_json_content_type_parameters_are_accepted(): void
    {
        $this->assertSame(
            ['name' => 'Example'],
            Url::bodyDecode('{"name":"Example"}', 'application/json; charset=utf-8')
        );
    }

    public function test_malformed_json_raises_a_bad_request_exception(): void
    {
        try {
            Url::bodyDecode('{"name":', 'application/json; charset=utf-8');
            $this->fail('Malformed JSON must be rejected.');
        } catch (InvalidRequestBodyException $exception) {
            $this->assertSame(400, $exception->getHttpStatus());
            $this->assertSame('INVALID_REQUEST_BODY', $exception->getErrorCode());
        }
    }
}
