<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\HtmlBuilder\AdminLte as LegacyAdminLte;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form as LegacyBt5Form;
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
}
