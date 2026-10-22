<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Libs\Env;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/src/framework/Helpers/env.php';

class EnvHelperPrecedenceTest extends TestCase
{
    private string $key = 'SIMPLEREST_TEST_ENV_HELPER_PRECEDENCE';
    private $originalProcessValue;
    private $originalEnvData;

    protected function setUp(): void
    {
        $this->originalProcessValue = getenv($this->key);
        $this->originalEnvData = Env::$data;
    }

    protected function tearDown(): void
    {
        if ($this->originalProcessValue === false) {
            putenv($this->key);
        } else {
            putenv($this->key . '=' . $this->originalProcessValue);
        }

        Env::$data = $this->originalEnvData;
    }

    public function test_process_environment_overrides_loaded_file_value(): void
    {
        Env::$data = [$this->key => 'file-value'];
        putenv($this->key . '=process-value');

        $this->assertSame('process-value', env($this->key));
    }

    public function test_missing_process_value_falls_back_to_loaded_file_value(): void
    {
        putenv($this->key);
        Env::$data = [$this->key => 'file-value'];

        $this->assertSame('file-value', env($this->key));
    }

    public function test_explicitly_empty_process_value_overrides_file_and_default(): void
    {
        Env::$data = [$this->key => 'file-value'];
        putenv($this->key . '=');

        $this->assertSame('', env($this->key, 'default-value'));
    }
}
