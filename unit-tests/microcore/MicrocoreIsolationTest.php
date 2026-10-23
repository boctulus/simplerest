<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Container;
use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\WebRouter;
use PHPUnit\Framework\TestCase;

final class MicrocoreIsolationTest extends TestCase
{
    protected function setUp(): void
    {
        Container::reset();
        WebRouter::reset();
    }

    public function testHealthRouteReturnsJsonWithoutApplicationConfiguration(): void
    {
        WebRouter::get('/health', static fn (Request $request): array => ['status' => 'ok']);

        $response = FrontController::handle(new Request('GET', '/health'));

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json; charset=utf-8', $response->getHeaderLine('content-type'));
        self::assertSame(['status' => 'ok'], json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testMissingRequiredMiddlewareStopsProtectedHandler(): void
    {
        $handlerRan = false;
        WebRouter::get(
            '/protected',
            static function () use (&$handlerRan): array {
                $handlerRan = true;
                return ['secret' => 'unavailable'];
            },
            ['Boctulus\\MissingAuthenticationProvider']
        );

        $response = FrontController::handle(new Request('GET', '/protected'));

        self::assertSame(503, $response->getStatusCode());
        self::assertFalse($handlerRan);
    }

    public function testCoreManifestHasNoOptionalRuntimeOrDefaultLoads(): void
    {
        $root = dirname(__DIR__, 2);
        $manifest = json_decode((string) file_get_contents($root . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(['php'], array_keys($manifest['require'] ?? []));
        self::assertArrayNotHasKey('require-dev', $manifest);
        self::assertArrayNotHasKey('suggest', $manifest);
        self::assertArrayNotHasKey('autoload-dev', $manifest);
        self::assertArrayNotHasKey('files', $manifest['autoload'] ?? []);

        $autoloadConfig = (string) file_get_contents($root . '/config/autoload.php');
        self::assertStringNotContainsString('src/framework/Helpers', $autoloadConfig);
    }

    public function testKernelHasNoOptionalPackageAliasesOrReferences(): void
    {
        $root = dirname(__DIR__, 2);
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            $root . '/src/framework',
            FilesystemIterator::SKIP_DOTS
        ));
        $source = '';
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $source .= file_get_contents($file->getPathname()) . "\n";
            }
        }

        self::assertDoesNotMatchRegularExpression('/class_alias\s*\(/i', $source);
        self::assertDoesNotMatchRegularExpression('/boctulus\\\\simplerest-(api-clients|web-inspection|bootstrap-html|scaffolding|fpdf)/i', $source);
        self::assertDoesNotMatchRegularExpression('/Firebase\\\\JWT|psr\\/http-message|Doctrine\\\\Inflector|PDO/i', $source);
        self::assertDoesNotMatchRegularExpression('/Bt5Form|AdminLte|BootstrapPaginator|Bootstrap/i', $source);

        foreach ([
            'Boctulus\\Simplerest\\Core\\Libs\\DB',
            'Boctulus\\Simplerest\\Core\\Api\\ApiController',
            'Boctulus\\Simplerest\\Core\\Security\\Acl',
            'Boctulus\\Simplerest\\Core\\Libs\\HtmlBuilder\\Bt5Form',
        ] as $optionalClass) {
            self::assertFalse(class_exists($optionalClass), $optionalClass . ' must not load from the microcore autoloader.');
        }
    }
}
