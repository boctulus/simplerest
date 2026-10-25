<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Libs\Env;
use PHPUnit\Framework\TestCase;

class EnvSetupTest extends TestCase
{
    private string $tempDir;
    private $originalAppEnv;
    private $originalEnvData;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-env-' . bin2hex(random_bytes(6));
        mkdir($this->tempDir, 0700, true);

        $this->originalAppEnv = getenv('APP_ENV');
        $this->originalEnvData = Env::$data;
        putenv('APP_ENV');
    }

    protected function tearDown(): void
    {
        if ($this->originalAppEnv === false) {
            putenv('APP_ENV');
        } else {
            putenv('APP_ENV=' . $this->originalAppEnv);
        }

        $resetPath = $this->writeFile('reset.env', '');
        Env::setup(file: $resetPath, inherit: false);
        Env::$data = $this->originalEnvData;

        foreach (new \DirectoryIterator($this->tempDir) as $entry) {
            if (!$entry->isDot() && $entry->isFile()) {
                unlink($entry->getPathname());
            }
        }

        rmdir($this->tempDir);
    }

    public function test_dev_env_overlays_base_in_memory_without_changing_either_file(): void
    {
        $baseContent = "APP_ENV=local\nDB_HOST=localhost\nDB_NAME=consorcios\n";
        $devContent  = "DB_NAME=goprop_testing\nDEBUG=true\n";
        $basePath    = $this->writeFile('.env', $baseContent);
        $devPath     = $this->writeFile('.dev-env', $devContent);

        Env::setup(file: $basePath);

        $this->assertSame('localhost', Env::get('DB_HOST'));
        $this->assertSame('goprop_testing', Env::get('DB_NAME'));
        $this->assertSame('true', Env::get('DEBUG'));
        $this->assertSame($baseContent, file_get_contents($basePath));
        $this->assertSame($devContent, file_get_contents($devPath));
    }

    public function test_dev_env_is_ignored_outside_local_or_development(): void
    {
        $basePath = $this->writeFile('.env', "APP_ENV=production\nBASE_ONLY=base\n");
        $this->writeFile('.dev-env', "DEV_ONLY=dev\n");

        Env::setup(file: $basePath);

        $this->assertSame('production', Env::get('APP_ENV'));
        $this->assertSame('base', Env::get('BASE_ONLY'));
        $this->assertNull(Env::get('DEV_ONLY'));
    }

    public function test_dev_env_cannot_override_app_env(): void
    {
        $basePath = $this->writeFile('.env', "APP_ENV=local\nDB_HOST=localhost\n");
        $this->writeFile('.dev-env', "APP_ENV=production\nDB_HOST=production-server\n");
        Env::$data = ['STALE_VALUE' => 'previous'];

        try {
            Env::setup(file: $basePath);
            $this->fail('Expected .dev-env to reject APP_ENV.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertStringContainsString('.dev-env must not define APP_ENV', $exception->getMessage());
            $this->assertNull(Env::$data);
        }

        try {
            Env::get('DB_HOST', 'fallback');
            $this->fail('Expected reads after failed setup to rethrow the setup error.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertStringContainsString('.dev-env must not define APP_ENV', $exception->getMessage());
        }

        Env::setup(file: $basePath, inherit: false);
        $this->assertSame('local', Env::get('APP_ENV'));
        $this->assertSame('localhost', Env::get('DB_HOST'));
        $this->assertNull(Env::get('STALE_VALUE'));
    }

    public function test_dev_env_is_ignored_when_app_env_is_missing(): void
    {
        $basePath = $this->writeFile('.env', "BASE_ONLY=base\n");
        $this->writeFile('.dev-env', "DEV_ONLY=dev\n");

        Env::setup(file: $basePath);

        $this->assertSame('base', Env::get('BASE_ONLY'));
        $this->assertNull(Env::get('DEV_ONLY'));
    }

    public function test_process_app_env_controls_dev_env_eligibility(): void
    {
        $basePath = $this->writeFile('.env', "APP_ENV=local\nBASE_ONLY=base\n");
        $this->writeFile('.dev-env', "DEV_ONLY=dev\n");
        putenv('APP_ENV=production');

        Env::setup(file: $basePath);

        $this->assertSame('production', Env::get('APP_ENV'));
        $this->assertSame('base', Env::get('BASE_ONLY'));
        $this->assertNull(Env::get('DEV_ONLY'));
    }

    public function test_empty_process_app_env_disables_overlay_and_process_local_enables_it(): void
    {
        $basePath = $this->writeFile('.env', "APP_ENV=production\nBASE_ONLY=base\n");
        $this->writeFile('.dev-env', "DEV_ONLY=dev\n");

        putenv('APP_ENV=local');
        Env::setup(file: $basePath);
        $this->assertSame('local', Env::get('APP_ENV'));
        $this->assertSame('dev', Env::get('DEV_ONLY'));

        putenv('APP_ENV=');
        Env::setup(file: $basePath);
        $this->assertSame('', Env::get('APP_ENV'));
        $this->assertNull(Env::get('DEV_ONLY'));
    }

    public function test_explicit_replacement_skips_base_inheritance_and_dev_overlay(): void
    {
        $basePath        = $this->writeFile('.env', "APP_ENV=local\nBASE_ONLY=base\n");
        $replacementPath = $this->writeFile('.env.testing', "TEST_ONLY=testing\n");
        $this->writeFile('.dev-env', "DEV_ONLY=dev\n");

        Env::setup(file: $basePath);
        Env::setup(file: $replacementPath, inherit: false);

        $this->assertSame('testing', Env::get('TEST_ONLY'));
        $this->assertSame('fallback', Env::get('BASE_ONLY', 'fallback'));
        $this->assertSame('fallback', Env::get('DEV_ONLY', 'fallback'));
    }

    public function test_process_environment_wins_and_empty_values_are_preserved(): void
    {
        $key = 'SIMPLEREST_TEST_ENV_PROCESS_OVERRIDE';
        $originalValue = getenv($key);
        $basePath = $this->writeFile('.env', "$key=file-value\nEMPTY_FILE_VALUE=file-value\n");

        try {
            putenv($key . '=');
            Env::setup(file: $basePath, inherit: false);

            $this->assertSame('', Env::get($key, 'fallback'));
            $this->assertSame('fallback', Env::get('MISSING_VALUE', 'fallback'));
            $this->assertSame('file-value', Env::get('EMPTY_FILE_VALUE', 'fallback'));
            $this->assertFalse(Env::getBool($key, true));
        } finally {
            if ($originalValue === false) {
                putenv($key);
            } else {
                putenv($key . '=' . $originalValue);
            }
        }
    }

    public function test_empty_overlay_value_overrides_the_base_value(): void
    {
        $basePath = $this->writeFile('.env', "APP_ENV=local\nOPTIONAL_VALUE=base\n");
        $this->writeFile('.dev-env', "OPTIONAL_VALUE=\n");

        Env::setup(file: $basePath);

        $this->assertSame('', Env::get('OPTIONAL_VALUE', 'fallback'));
        $this->assertFalse(Env::getBool('OPTIONAL_VALUE', true));
    }

    public function test_repeated_setup_does_not_leak_values_from_previous_file(): void
    {
        $firstPath  = $this->writeFile('.env', "APP_ENV=local\nFIRST_ONLY=first\n");
        $secondPath = $this->writeFile('.env.testing', "SECOND_ONLY=second\n");

        Env::setup(file: $firstPath);
        Env::setup(file: $secondPath, inherit: false);

        $this->assertSame('second', Env::get('SECOND_ONLY'));
        $this->assertSame('fallback', Env::get('FIRST_ONLY', 'fallback'));
    }

    public function test_required_keys_are_validated_after_file_resolution(): void
    {
        $basePath = $this->writeFile('.env', "APP_ENV=production\nREQUIRED_VALUE=\n");
        Env::$data = ['STALE_VALUE' => 'previous'];

        try {
            Env::setup(file: $basePath, inherit: false, required: ['REQUIRED_VALUE', 'MISSING_VALUE']);
            $this->fail('Expected empty and missing required values to fail validation.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('REQUIRED_VALUE', $exception->getMessage());
            $this->assertStringContainsString('MISSING_VALUE', $exception->getMessage());
            $this->assertNull(Env::$data);
        }

        try {
            Env::get('STALE_VALUE', 'fallback');
            $this->fail('Expected reads after failed setup to rethrow the setup error.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Required environment variables are missing or empty', $exception->getMessage());
        }

        $recoveryPath = $this->writeFile('.env.recovered', "RECOVERED=yes\n");
        Env::setup(file: $recoveryPath, inherit: false);
        $this->assertSame('yes', Env::get('RECOVERED'));
        $this->assertNull(Env::get('STALE_VALUE'));
    }

    public function test_parse_failure_remains_failed_until_setup_is_retried(): void
    {
        Env::$data = ['STALE_VALUE' => 'previous'];
        $missingPath = $this->tempDir . DIRECTORY_SEPARATOR . 'missing.env';

        try {
            Env::setup(file: $missingPath, inherit: false);
            $this->fail('Expected a missing environment file to fail setup.');
        } catch (\Exception $exception) {
            $this->assertStringContainsString('Cannot read env file', $exception->getMessage());
            $this->assertNull(Env::$data);
        }

        try {
            Env::get('STALE_VALUE', 'fallback');
            $this->fail('Expected reads after failed setup to rethrow the setup error.');
        } catch (\Exception $exception) {
            $this->assertStringContainsString('Cannot read env file', $exception->getMessage());
        }

        $recoveryPath = $this->writeFile('.env.after-parse-error', "RECOVERED=yes\n");
        Env::setup(file: $recoveryPath, inherit: false);
        $this->assertSame('yes', Env::get('RECOVERED'));
        $this->assertNull(Env::get('STALE_VALUE'));
    }

    private function writeFile(string $name, string $contents): string
    {
        $path = $this->tempDir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $contents);

        return $path;
    }
}
