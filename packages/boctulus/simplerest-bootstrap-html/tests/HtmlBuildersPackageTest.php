<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\HtmlBuilder\AdminLte as LegacyAdminLte;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form as LegacyBt5Form;
use Boctulus\Simplerest\Core\Libs\BootstrapPaginator as LegacyBootstrapPaginator;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator;
use Boctulus\Simplerest\HtmlBuilders\AdminLte;
use Boctulus\Simplerest\HtmlBuilders\Bt5Form;
use PHPUnit\Framework\TestCase;

final class HtmlBuildersPackageTest extends TestCase
{
    public function testBootstrapBuilderRendersAFormInput(): void
    {
        $html = Bt5Form::inputText('account@example.test', ['name' => 'email']);

        self::assertStringContainsString('email', $html);
        self::assertStringContainsString('account@example.test', $html);
    }

    public function testAdminLteBuilderKeepsItsBootstrapParentContract(): void
    {
        self::assertTrue(is_subclass_of(AdminLte::class, Bt5Form::class));
        self::assertStringContainsString('ready', AdminLte::alert('ready'));
    }

    public function testFormerCoreNamesRemainUsableWhenCoreProvidesThemOrThePackageAlias(): void
    {
        self::assertTrue(class_exists(LegacyBt5Form::class));
        self::assertTrue(class_exists(LegacyAdminLte::class));
        self::assertStringContainsString('email', LegacyBt5Form::inputText(null, ['name' => 'email']));
        self::assertStringContainsString('ready', LegacyAdminLte::alert('ready'));
    }

    public function testBootstrapPaginatorRendersWithItsLegacyCoreName(): void
    {
        $configProperty = new ReflectionProperty(Config::class, 'data');
        $oldConfig = $configProperty->getValue();
        $configProperty->setValue(null, ['paginator' => []]);

        try {
            foreach ([BootstrapPaginator::class, LegacyBootstrapPaginator::class] as $class) {
                $bufferLevel = ob_get_level();
                ob_start();
                try {
                    $class::render(['paginator' => ['last_page' => 3, 'current_page' => 2]]);
                    $html = (string) ob_get_contents();
                    self::assertStringContainsString('>1</a>', $html);
                    self::assertStringContainsString('>2</a>', $html);
                    self::assertStringContainsString('>3</a>', $html);
                } finally {
                    while (ob_get_level() > $bufferLevel) {
                        ob_end_clean();
                    }
                }
            }
        } finally {
            $configProperty->setValue(null, $oldConfig);
        }
    }
}
