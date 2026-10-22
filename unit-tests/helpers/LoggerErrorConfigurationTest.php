<?php

namespace Boctulus\Simplerest\Tests\Helpers;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Logger;
use PHPUnit\Framework\TestCase;

class LoggerErrorConfigurationTest extends TestCase
{
    private \ReflectionProperty $configData;
    private array $originalConfigData;
    private \ReflectionProperty $errorLogFilename;
    private string $originalErrorLogFilename;
    private string $temporaryDirectory;
    private string $logPath;

    protected function setUp(): void
    {
        $this->configData = new \ReflectionProperty(Config::class, 'data');
        $this->originalConfigData = $this->configData->getValue();
        $this->errorLogFilename = new \ReflectionProperty(Logger::class, 'err_logfile');
        $this->originalErrorLogFilename = $this->errorLogFilename->getValue();

        $this->temporaryDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'simplerest_log_errors_' . bin2hex(random_bytes(8));

        if (!mkdir($this->temporaryDirectory, 0700)) {
            self::fail('Could not create isolated temporary log directory.');
        }

        $this->logPath = $this->temporaryDirectory . DIRECTORY_SEPARATOR . 'errors.log';
        Logger::setErrFilename($this->logPath);

        if (!function_exists('log_error')) {
            require_once dirname(__DIR__, 2) . '/src/framework/Helpers/loggers.php';
        }
    }

    protected function tearDown(): void
    {
        $this->configData->setValue(null, $this->originalConfigData);
        $this->errorLogFilename->setValue(null, $this->originalErrorLogFilename);

        if (is_file($this->logPath)) {
            unlink($this->logPath);
        }

        if (is_dir($this->temporaryDirectory)) {
            rmdir($this->temporaryDirectory);
        }
    }

    public function test_log_error_is_enabled_by_log_errors_when_debug_is_disabled(): void
    {
        $this->configData->setValue(null, ['debug' => false, 'log_errors' => true]);

        log_error('error logging remains enabled outside debug mode');

        self::assertFileExists($this->logPath);
        self::assertStringContainsString(
            'error logging remains enabled outside debug mode',
            file_get_contents($this->logPath)
        );
    }

    public function test_log_error_is_disabled_when_log_errors_is_false_even_in_debug_mode(): void
    {
        $this->configData->setValue(null, ['debug' => true, 'log_errors' => false]);

        log_error('this message must not be written');

        self::assertFileDoesNotExist($this->logPath);
    }
}
