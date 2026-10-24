<?php

namespace Boctulus\Simplerest\Tests\Css;

use Boctulus\Simplerest\Core\Libs\HtmlMinifier;
use PHPUnit\Framework\TestCase;

class CssRemoveClassesTest extends TestCase
{
    public function testRemovesExactClassTokensAtAnyPositionAndKeepsSimilarNames(): void
    {
        $html = '<span class="remove-first keep remove-last remove-extra"></span>';

        self::assertSame(
            '<span class="remove-extra"></span>',
            HtmlMinifier::removeCSSClasses($html, ['remove-first', 'keep', 'remove-last'])
        );
    }

    public function testDoesNotRewriteAttributesWithoutMatchingClasses(): void
    {
        $html = '<span class="keep  keep-extra"></span>';

        self::assertSame($html, HtmlMinifier::removeCSSClasses($html, ['keep-not-this']));
    }
}
