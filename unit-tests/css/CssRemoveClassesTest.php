<?php

namespace Boctulus\Simplerest\Tests\Css;

use Boctulus\Simplerest\Core\Libs\CSS;
use PHPUnit\Framework\TestCase;

class CssRemoveClassesTest extends TestCase
{
    public function testRemovesExactClassTokensAtAnyPositionAndKeepsSimilarNames(): void
    {
        $html = '<span class="remove-first keep remove-last remove-extra"></span>';

        self::assertSame(
            '<span class="remove-extra"></span>',
            CSS::removeCSSClasses($html, ['remove-first', 'keep', 'remove-last'])
        );
    }

    public function testDoesNotRewriteAttributesWithoutMatchingClasses(): void
    {
        $html = '<span class="keep  keep-extra"></span>';

        self::assertSame($html, CSS::removeCSSClasses($html, ['keep-not-this']));
    }
}
