<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\CodeReducer;
use Boctulus\Simplerest\Core\Libs\HtmlMinifier;
use PHPUnit\Framework\TestCase;

final class CodeReducerPackageTest extends TestCase
{
    public function testReducerKeepsRequestedFunctionsAndDropsOthers(): void
    {
        $source = <<<'PHP'
<?php
function keepMe(): string { return 'kept'; }
function removeMe(): string { return 'removed'; }
PHP;

        $reduced = (new CodeReducer())->reduceCode($source, ['keepMe']);

        self::assertStringContainsString('function keepMe()', $reduced);
        self::assertStringNotContainsString('function removeMe()', $reduced);
    }

    public function testHtmlMinifierRemovesOnlySelectedClasses(): void
    {
        $html = '<div class="keep remove"></div>';

        self::assertSame(
            '<div class="keep"></div>',
            HtmlMinifier::removeCSSClasses($html, ['remove'])
        );
    }
}
