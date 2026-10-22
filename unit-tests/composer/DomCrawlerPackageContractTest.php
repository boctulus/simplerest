<?php

namespace Boctulus\Simplerest\Tests\Composer;

use Boctulus\Simplerest\Core\Libs\DomCrawler;
use PHPUnit\Framework\TestCase;

class DomCrawlerPackageContractTest extends TestCase
{
    public function test_dom_crawler_preserves_the_framework_wrapper_api(): void
    {
        $crawler = new DomCrawler('<html><body><a class="selected" href="/consumer">Consumer</a></body></html>');

        $this->assertTrue($crawler->exists('a.selected'));
        $this->assertSame('/consumer', $crawler->getAttr('a.selected', 'href'));
        $this->assertSame('Consumer', $crawler->getText('a.selected'));
        $this->assertSame(['/consumer'], $crawler->getLinks());
    }
}
