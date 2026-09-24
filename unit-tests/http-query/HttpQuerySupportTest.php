<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\CorsHandler;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\WebRouter;

require_once __DIR__ . '/../../config/constants.php';

final class HttpQuerySupportTest extends TestCase
{
    private array $serverBackup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serverBackup = $_SERVER;
        $this->resetRouter();
        $this->resetRequest();
        $this->setStaticProperty(Config::class, 'data', [
            'method_override' => [
                'by_url' => false,
                'by_header' => false,
            ],
        ]);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function testQueryRouteCanBeRegistered(): void
    {
        WebRouter::query('products/search', static fn () => ['ok' => true]);

        $routes = WebRouter::getRoutes();

        $this->assertArrayHasKey('QUERY', $routes);
        $this->assertArrayHasKey('products/search', $routes['QUERY']);
    }

    public function testAnyAndFromArrayIncludeQuery(): void
    {
        WebRouter::any('health', static fn () => ['ok' => true]);
        WebRouter::fromArray([
            'QUERY:reports/search' => static fn () => ['ok' => true],
        ]);

        $routes = WebRouter::getRoutes();

        $this->assertArrayHasKey('health', $routes['QUERY']);
        $this->assertArrayHasKey('reports/search', $routes['QUERY']);
    }

    public function testPutRouteKeepsCurrentUriForNaming(): void
    {
        WebRouter::put('products/{id}', static fn () => ['ok' => true])
            ->name('products.update');

        $aliases = $this->getStaticProperty(WebRouter::class, 'aliases');

        $this->assertSame('PUT', $aliases['products.update']['verb']);
        $this->assertSame('products/{id}', $aliases['products.update']['uri']);
    }

    public function testRequestNormalizesMethodsHeadersAndMediaTypes(): void
    {
        $headers = Request::normalizeHeaders([
            'Content-Type' => 'Application/JSON; Charset=UTF-8',
            'X-Request-ID' => 'query-test',
        ]);

        $this->assertSame('QUERY', Request::normalizeMethod('query'));
        $this->assertSame('Application/JSON; Charset=UTF-8', $headers['content-type']);
        $this->assertSame('query-test', $headers['x-request-id']);
        $this->assertSame(
            'application/json',
            Request::extractMediaType($headers['content-type'])
        );
    }

    public function testValidQueryRequestAcceptsJsonWithCharset(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'QUERY';
        $this->setStaticProperty(Request::class, 'content_type', 'application/json; charset=utf-8');
        $this->setStaticProperty(Request::class, 'raw', '{"status":"active"}');
        $this->setStaticProperty(Request::class, 'body', ['status' => 'active']);

        Request::getInstance()->validateQueryRequest();

        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider invalidQueryRequestProvider
     */
    public function testInvalidQueryRequestReturnsExpectedStatus(
        ?string $contentType,
        string $raw,
        $body,
        ?string $bodyError,
        int $expectedStatus
    ): void {
        $_SERVER['REQUEST_METHOD'] = 'QUERY';
        $this->setStaticProperty(Request::class, 'content_type', $contentType);
        $this->setStaticProperty(Request::class, 'raw', $raw);
        $this->setStaticProperty(Request::class, 'body', $body);
        $this->setStaticProperty(Request::class, 'body_error', $bodyError);

        try {
            Request::getInstance()->validateQueryRequest();
            $this->fail('Expected QUERY request validation to fail');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame($expectedStatus, $exception->getCode());
        }
    }

    public static function invalidQueryRequestProvider(): array
    {
        return [
            'missing content type' => [null, '{"status":"active"}', ['status' => 'active'], null, 400],
            'unsupported media type' => ['text/plain', 'status=active', 'status=active', null, 415],
            'missing body' => ['application/json', '', null, null, 400],
            'malformed JSON' => ['application/json', '{', null, 'Invalid JSON body: Syntax error', 400],
            'scalar JSON content' => ['application/json', '"active"', 'active', null, 422],
        ];
    }

    public function testCorsWildcardIncludesQuery(): void
    {
        $handler = new CorsHandler();
        $allowedMethods = $this->getProperty($handler, 'allowedMethods');

        $this->assertContains('QUERY', $allowedMethods);
        $this->assertContains('HEAD', $allowedMethods);
    }

    public function testCorsPathMatchesLeadingSlashRequestUri(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/products';
        $handler = new CorsHandler(['api/*']);

        $reflection = new ReflectionClass($handler);
        $method = $reflection->getMethod('isPathAllowed');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($handler));
    }

    public function testApiClientAcceptsQueryMethod(): void
    {
        $reflection = new ReflectionClass(ApiClient::class);
        $client = $reflection->newInstanceWithoutConstructor();

        $client->setMethod('query');

        $this->assertSame('QUERY', $this->getProperty($client, 'verb'));
        $this->assertSame('QUERY', ApiClient::HTTP_METH_QUERY);
    }

    private function resetRouter(): void
    {
        $reflection = new ReflectionClass(WebRouter::class);

        foreach ([
            'routes' => [],
            'params' => [],
            'current' => [],
            'wheres' => [],
            'ck_params' => [],
            'ctrls' => [],
            'current_verb' => null,
            'current_uri' => null,
            'aliases' => [],
            'v_aliases' => [],
            'routePatterns' => [],
            'routeParamNames' => [],
            'groupPrefix' => '',
        ] as $property => $value) {
            $this->setStaticProperty(WebRouter::class, $property, $value);
        }

        $this->setStaticProperty(
            WebRouter::class,
            'instance',
            $reflection->newInstanceWithoutConstructor()
        );
    }

    private function resetRequest(): void
    {
        $reflection = new ReflectionClass(Request::class);

        foreach ([
            'query_arr' => [],
            'raw' => null,
            'body' => null,
            'body_error' => null,
            'params' => [],
            'headers' => [],
            'accept_encoding' => null,
            'content_type' => null,
        ] as $property => $value) {
            $this->setStaticProperty(Request::class, $property, $value);
        }

        $this->setStaticProperty(
            Request::class,
            'instance',
            $reflection->newInstanceWithoutConstructor()
        );
    }

    private function setStaticProperty(string $class, string $name, $value): void
    {
        $reflection = new ReflectionClass($class);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue(null, $value);
    }

    private function getStaticProperty(string $class, string $name)
    {
        $reflection = new ReflectionClass($class);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue();
    }

    private function getProperty(object $object, string $name)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
