<?php

namespace Boctulus\Simplerest\Tests\Helpers;

use Boctulus\Simplerest\Core\Libs\Config;
use PHPUnit\Framework\TestCase;

class AssetPathTest extends TestCase
{
    private \ReflectionProperty $configData;
    private array $originalConfigData;

    protected function setUp(): void
    {
        $this->configData = new \ReflectionProperty(Config::class, 'data');
        $this->originalConfigData = $this->configData->getValue();

        require_once dirname(__DIR__, 2) . '/src/framework/Helpers/system.php';

        if (!function_exists('asset')) {
            require_once dirname(__DIR__, 2) . '/src/framework/Helpers/view.php';
        }
    }

    protected function tearDown(): void
    {
        $this->configData->setValue(null, $this->originalConfigData);
    }

    public function test_asset_resolves_under_public_assets_from_project_root_document_root(): void
    {
        $this->configData->setValue(null, ['app_url' => 'https://app.example.test/base']);

        self::assertSame(
            'https://app.example.test/base/public/assets/css/app.css',
            asset('/css/app.css')
        );
    }
}
