<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * The microcore documentation describes the HTTP path (index -> WebRouter ->
 * FrontController -> controller -> output -> flush) but the certified suite did
 * not execute it. This smoke test boots the fixture with PHP's built-in server
 * and asserts the observable contract: JSON bodies, status codes, route
 * parameters, front-controller dispatch and error handling.
 */
final class HttpFlowSmokeTest extends TestCase
{
    /** @var resource|null */
    private static $server = null;

    private static int $port = 0;

    public static function setUpBeforeClass(): void
    {
        $fixture = __DIR__ . '/fixtures/http-flow/index.php';
        self::assertFileExists($fixture);

        self::$port = self::freePort();

        $command = [PHP_BINARY, '-S', '127.0.0.1:' . self::$port, $fixture];
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', sys_get_temp_dir() . '/simplerest-http-flow.out', 'a'],
            2 => ['file', sys_get_temp_dir() . '/simplerest-http-flow.err', 'a'],
        ];

        self::$server = proc_open($command, $descriptors, $pipes);

        if (!is_resource(self::$server)) {
            self::fail('No se pudo iniciar el servidor PHP para el smoke test.');
        }

        $deadline = microtime(true) + 5.0;
        while (microtime(true) < $deadline) {
            $socket = @fsockopen('127.0.0.1', self::$port, $errno, $errstr, 0.2);
            if (is_resource($socket)) {
                fclose($socket);
                return;
            }
            usleep(50_000);
        }

        self::fail('El servidor PHP no respondio en 5 segundos.');
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$server)) {
            proc_terminate(self::$server);
            proc_close(self::$server);
            self::$server = null;
        }
    }

    public function testHealthRouteReturnsJson(): void
    {
        [$status, $headers, $body] = $this->request('/system/health');

        self::assertSame(200, $status);
        self::assertSame(['ok' => true], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testWebRouterPassesRouteParameters(): void
    {
        [$status, , $body] = $this->request('/web/echo/42');

        self::assertSame(200, $status);
        self::assertSame(42, json_decode($body, true, 512, JSON_THROW_ON_ERROR)['id'] ?? null);
    }

    public function testFrontControllerDispatchesAnOrdinaryController(): void
    {
        [$status, , $body] = $this->request('/echo/index');

        self::assertSame(200, $status);
        self::assertSame('index', json_decode($body, true, 512, JSON_THROW_ON_ERROR)['action'] ?? null);
    }

    public function testFrontControllerPassesPathArguments(): void
    {
        [$status, , $body] = $this->request('/echo/show/7');

        self::assertSame(200, $status);
        self::assertSame(7, json_decode($body, true, 512, JSON_THROW_ON_ERROR)['id'] ?? null);
    }

    public function testUnknownControllerReturns404(): void
    {
        [$status] = $this->request('/does-not-exist/x');

        self::assertSame(404, $status);
    }

    public function testControllerExceptionIsRenderedAs500Json(): void
    {
        [$status, $headers, $body] = $this->request('/echo/boom');

        self::assertSame(500, $status);
        self::assertStringContainsString('application/json', strtolower($headers));
        self::assertArrayHasKey('error', json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    /** @return array{0:int,1:string,2:string} */
    private function request(string $path): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Accept: application/json\r\n",
                'ignore_errors' => true,
                'timeout' => 5,
            ],
        ]);

        $body = (string) @file_get_contents('http://127.0.0.1:' . self::$port . $path, false, $context);
        $headers = implode("\n", $http_response_header ?? []);
        $status = 0;

        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $headers, $matches) === 1) {
            $status = (int) $matches[1];
        }

        return [$status, $headers, $body];
    }

    private static function freePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);

        if (!is_resource($socket)) {
            self::fail("No se pudo reservar un puerto: $errstr");
        }

        $name = stream_socket_get_name($socket, false);
        fclose($socket);

        return (int) substr((string) $name, strrpos((string) $name, ':') + 1);
    }
}
