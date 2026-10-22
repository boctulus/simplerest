<?php

namespace Boctulus\Simplerest\Tests\Compatibility;

use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Libs\AndroidCodeAnalyzer;
use Boctulus\Simplerest\Modules\AndroidEngine\Src\Libs\CodeStackDetector;
use PHPUnit\Framework\TestCase;

class FrameworkTraitConstantsTest extends TestCase
{
    private static ?\Closure $moduleAutoloader = null;

    public static function setUpBeforeClass(): void
    {
        self::$moduleAutoloader = static function (string $class): void {
            $prefix = 'Boctulus\\Simplerest\\Modules\\AndroidEngine\\Src\\';

            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }

            $relativeClass = substr($class, strlen($prefix));
            $path = dirname(__DIR__, 2) . '/app/Modules/AndroidEngine/src/'
                . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (is_file($path)) {
                require_once $path;
            }
        };

        spl_autoload_register(self::$moduleAutoloader, true, true);
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$moduleAutoloader !== null) {
            spl_autoload_unregister(self::$moduleAutoloader);
            self::$moduleAutoloader = null;
        }
    }

    public function test_framework_classes_keep_their_public_constants(): void
    {
        self::assertSame(0, Model::EXECUTION_MODE_NORMAL);
        self::assertSame(1, Model::EXECUTION_MODE_SIMULATE);
        self::assertSame(2, Model::EXECUTION_MODE_PREVIEW);
        self::assertFalse(Model::DECIMAL_AS_STRING);

        self::assertSame(1, Logger::SEVERITY_ERROR);
        self::assertSame(2, Logger::SEVERITY_WARNING);
        self::assertSame(4, Logger::SEVERITY_INFO);
        self::assertSame(8, Logger::SEVERITY_DEBUG);
        self::assertSame(4, (new Logger())->getInfoLevel());

        self::assertSame(2, AndroidCodeAnalyzer::SEVERITY_WARNING);
        self::assertSame(2, CodeStackDetector::SEVERITY_WARNING);
    }
}
