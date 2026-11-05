<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Controllers\WebController;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Date;
use Boctulus\Simplerest\Core\Libs\Env;
use Boctulus\Simplerest\Core\Libs\MySQLDatabaseBackup;
use Boctulus\Simplerest\Core\Libs\Paginator;
use Boctulus\Simplerest\Core\Libs\SortedIterator;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Response;
use PHPUnit\Framework\TestCase;

/**
 * Contract for the two core methods that blocked a clean PHPStan run on the
 * 8.3 baseline. Both are runtime-visible, so they get behavioural tests rather
 * than a baseline entry.
 */
final class Php83CompatibilityTest extends TestCase
{
    public function testSortedIteratorDeclaresACovariantCompareReturnType(): void
    {
        $method = new ReflectionMethod(SortedIterator::class, 'compare');

        self::assertSame('int', (string) $method->getReturnType());
    }

    public function testSortedIteratorSortsByRealPath(): void
    {
        $dir = sys_get_temp_dir() . '/simplerest-sorted-' . bin2hex(random_bytes(4));
        mkdir($dir, 0775, true);

        $names = ['c.txt', 'a.txt', 'b.txt'];
        foreach ($names as $name) {
            file_put_contents($dir . '/' . $name, $name);
        }

        $files = [];
        foreach ($names as $name) {
            $files[] = new SplFileInfo($dir . '/' . $name);
        }

        $sorted = [];
        foreach (new SortedIterator(new ArrayIterator($files)) as $file) {
            $sorted[] = $file->getFilename();
        }

        foreach ($names as $name) {
            unlink($dir . '/' . $name);
        }
        rmdir($dir);

        self::assertCount(3, $sorted);
        self::assertSame(['a.txt', 'b.txt', 'c.txt'], $sorted);
    }

    public function testNextNthWeekDayReturnsAFormattedDateForEveryWeekday(): void
    {
        for ($weekday = 0; $weekday <= 6; $weekday++) {
            $result = Date::nextNthWeekDay($weekday, '', 'Y-m-d');

            self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result, "weekday=$weekday");
        }
    }

    public function testNextNthWeekDayMatchesTheRequestedWeekday(): void
    {
        for ($weekday = 0; $weekday <= 6; $weekday++) {
            $result = Date::nextNthWeekDay($weekday, '', 'w');

            self::assertSame((string) $weekday, $result);
        }
    }

    public function testUnravelEncodingKeepsTheUtf8DecodeBehaviour(): void
    {
        self::assertSame('caf?', Strings::unravelEncoding('café'));
        self::assertSame('plain ascii', Strings::unravelEncoding('plain ascii'));
        self::assertSame('?uro', Strings::unravelEncoding('€uro'));
        self::assertSame("control?byte", Strings::unravelEncoding("control\x80byte"));
    }

    /**
     * @dataProvider sensitiveParameterProvider
     */
    public function testSecretsAreMarkedAsSensitiveParameters(string $class, string $method, string $parameter): void
    {
        $reflection = new ReflectionParameter([$class, $method], $parameter);

        self::assertNotEmpty(
            $reflection->getAttributes(SensitiveParameter::class),
            "$class::$method(\$$parameter) must use #[\\SensitiveParameter]"
        );
    }

    public static function sensitiveParameterProvider(): array
    {
        return [
            'mysqldump password' => [MySQLDatabaseBackup::class, 'export', 'password'],
            'basic auth password' => [ApiClient::class, 'setBasicAuth', 'password'],
            'jwt token' => [ApiClient::class, 'setJWTAuth', 'token_jwt'],
            'http login password' => [ApiClient::class, 'httpLogin', 'password'],
            'proxy login password' => [ApiClient::class, 'proxyLogin', 'password'],
            'ftp password' => [ApiClient::class, 'simpleFtpGet', 'password'],
        ];
    }

    public function testCoreHasNoKnownDeprecatedCalls(): void
    {
        $core = dirname(__DIR__, 2) . '/src/framework/';
        $forbidden = [
            'utf8_decode(' => 'use mb_convert_encoding(..., \'ISO-8859-1\', \'UTF-8\')',
            'utf8_encode(' => 'use mb_convert_encoding(..., \'UTF-8\', \'ISO-8859-1\')',
            '->setAccessible(' => 'ReflectionProperty::setAccessible() is a no-op since PHP 8.1',
            'FILTER_SANITIZE_STRING' => 'removed in PHP 8.1',
            'create_function(' => 'removed in PHP 8.0',
            'strftime(' => 'deprecated in PHP 8.1',
        ];

        $found = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($core, FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/Templates/')) {
                continue;
            }

            $source = (string) file_get_contents($file->getPathname());
            foreach ($forbidden as $needle => $replacement) {
                if (str_contains($source, $needle)) {
                    $found[] = str_replace($core, '', $file->getPathname()) . ": $needle -> $replacement";
                }
            }
        }

        self::assertSame([], $found);
    }

    public function testEnvSetDoesNotDeclareARequiredParameterAfterAnOptionalOne(): void
    {
        $parameters = (new ReflectionMethod(Env::class, 'set'))->getParameters();
        $seenOptional = false;

        foreach ($parameters as $parameter) {
            if ($parameter->isOptional()) {
                $seenOptional = true;
                continue;
            }

            self::assertFalse($seenOptional, "Required \${$parameter->getName()} follows an optional parameter.");
        }
    }

    public function testEveryCoreFileDeclaresStrictTypes(): void
    {
        $core = dirname(__DIR__, 2) . '/src/framework/';
        $missing = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($core, FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/Templates/')) {
                continue;
            }

            $source = (string) file_get_contents($file->getPathname());
            if (!str_contains($source, 'declare(strict_types=1)')) {
                $missing[] = str_replace($core, '', $file->getPathname());
            }
        }

        self::assertSame([], $missing);
    }

    public function testDriverConstantsAreTyped(): void
    {
        foreach (['MYSQL', 'SQLITE', 'PGSQL', 'SQLSRV', 'INFOMIX', 'DB2', 'ORACLE', 'SYBASE', 'FIREBIRD'] as $name) {
            $constant = new ReflectionClassConstant(DB::class, $name);
            self::assertTrue($constant->hasType(), "DB::$name must be typed");
            self::assertSame('string', (string) $constant->getType());
        }
    }

    public function testPaginatorAndModelConstantsAreTyped(): void
    {
        self::assertSame('string', (string) (new ReflectionClassConstant(Paginator::class, 'TOP'))->getType());
        self::assertSame('int', (string) (new ReflectionClassConstant(Model::class, 'EXECUTION_MODE_NORMAL'))->getType());
        self::assertSame('bool', (string) (new ReflectionClassConstant(Model::class, 'DECIMAL_AS_STRING'))->getType());
    }

    public function testOverriddenMethodsUseTheOverrideAttribute(): void
    {
        $targets = [
            [Response::class, 'offsetGet'],
            [Response::class, 'offsetSet'],
            [SortedIterator::class, 'compare'],
        ];

        foreach ($targets as [$class, $method]) {
            $reflection = new ReflectionMethod($class, $method);
            self::assertNotEmpty($reflection->getAttributes(\Override::class), "$class::$method");
        }
    }

    public function testResponseEncodesWithoutPrettyPrinting(): void
    {
        $response = (new ReflectionClass(Response::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(Response::class, 'do_encode');

        self::assertSame('{"ok":true}', $method->invoke($response, ['ok' => true]));
    }

    public function testCoreHasNoImplicitNullableParameters(): void
    {
        $core = dirname(__DIR__, 2) . '/src/framework/';
        $pattern = '/function\s+\w+\s*\([^)]*(?<![\?\w\\\\])(?:int|string|bool|float|array|object|callable|iterable|[A-Z][A-Za-z0-9_\\\\]*)\s+\$\w+\s*=\s*null/s';

        $found = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($core, FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/Templates/')) {
                continue;
            }

            $source = (string) file_get_contents($file->getPathname());
            if (preg_match($pattern, $source) === 1) {
                $found[] = str_replace($core, '', $file->getPathname());
            }
        }

        self::assertSame([], $found, 'Implicit nullable parameters are deprecated in PHP 8.4.');
    }
}
