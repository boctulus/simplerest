<?php

namespace Boctulus\Simplerest\Tests\Http;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Url;
use PHPUnit\Framework\TestCase;

class UrlAppUrlTest extends TestCase
{
    private \ReflectionProperty $configData;
    private $originalConfigData;

    protected function setUp(): void
    {
        $this->configData = new \ReflectionProperty(Config::class, 'data');
        $this->originalConfigData = $this->configData->getValue();
    }

    protected function tearDown(): void
    {
        $this->configData->setValue(null, $this->originalConfigData);
    }

    public function test_get_app_url_returns_configured_url_without_trailing_slash(): void
    {
        $this->configData->setValue(null, ['app_url' => 'https://app.example.test/base/']);

        $this->assertSame('https://app.example.test/base', Url::getAppUrl());
    }

    public function test_get_app_url_returns_empty_string_when_not_configured(): void
    {
        $this->configData->setValue(null, ['app_url' => '']);

        $this->assertSame('', Url::getAppUrl());
    }
}
