<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Handlers\ErrorHandler;

require_once __DIR__ . '/../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
    return;
}

require_once __DIR__ . '/../../app.php';

/**
 * Accept negotiation used by ErrorHandler to choose a JSON or HTML error body.
 *
 * Regression: Request::acceptsJson() was removed while ErrorHandler still called it, so any
 * exception reaching ErrorHandler over HTTP died with "Call to undefined method" and the
 * client got a PHP fatal rendered as HTML with status 200.
 */
class RequestAcceptNegotiationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Request::setInstance(null);
    }

    protected function tearDown(): void
    {
        Request::setInstance(null);
        parent::tearDown();
    }

    private function withAccept(?string $accept): void
    {
        $request = Request::getInstance();
        Request::setInstance($accept === null ? $request : $request->withHeader('Accept', $accept));
    }

    public function testErrorHandlerOnlyCallsExistingRequestMethods(): void
    {
        $source = file_get_contents((new \ReflectionClass(ErrorHandler::class))->getFileName());
        preg_match_all('/Request::(\w+)\s*\(/', $source, $calls);

        $this->assertNotEmpty($calls[1]);
        foreach (array_unique($calls[1]) as $method) {
            $this->assertTrue(method_exists(Request::class, $method), "ErrorHandler calls missing Request::{$method}()");
        }
    }

    public function testAcceptsJson(): void
    {
        $cases = [
            ['application/json', true],
            ['application/json, text/plain, */*', true],
            ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', false],
            ['*/*', true],
            ['application/*', true],
            ['text/plain', false],
            [null, false],
        ];

        foreach ($cases as [$accept, $expected]) {
            Request::setInstance(null);
            $this->withAccept($accept);
            $this->assertSame($expected, Request::acceptsJson(), 'Accept: ' . var_export($accept, true));
        }
    }

    public function testAcceptsHtml(): void
    {
        $cases = [
            ['text/html,application/xhtml+xml,*/*;q=0.8', true],
            ['application/xhtml+xml', true],
            ['application/json', false],
            [null, false],
        ];

        foreach ($cases as [$accept, $expected]) {
            Request::setInstance(null);
            $this->withAccept($accept);
            $this->assertSame($expected, Request::acceptsHtml(), 'Accept: ' . var_export($accept, true));
        }
    }
}
