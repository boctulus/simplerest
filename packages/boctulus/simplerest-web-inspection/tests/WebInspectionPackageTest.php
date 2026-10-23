<?php

declare(strict_types=1);

use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Magento;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\PrestaShop;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Shopify;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Squarespace;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Wix;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\WordPress;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\Scanner;
use Boctulus\Simplerest\Integrations\Libs\DomCrawler;
use PHPUnit\Framework\TestCase;

final class WebInspectionPackageTest extends TestCase
{
    /** @var array<string, string> */
    private const LEGACY_CLASSES = [
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\Scanner' => Scanner::class,
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Magento' => Magento::class,
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\PrestaShop' => PrestaShop::class,
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Shopify' => Shopify::class,
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Squarespace' => Squarespace::class,
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Wix' => Wix::class,
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\WordPress' => WordPress::class,
        'Boctulus\\Simplerest\\Core\\Libs\\DomCrawler' => DomCrawler::class,
    ];

    public function testWebInspectionClassesKeepTheirLegacyNamesAndPublicMethods(): void
    {
        foreach (self::LEGACY_CLASSES as $legacy => $implementation) {
            self::assertTrue(class_exists($implementation), $implementation);
            self::assertTrue(class_exists($legacy), $legacy);
            $path = str_replace('\\', '/', (string) (new ReflectionClass($implementation))->getFileName());
            self::assertStringContainsString('/simplerest-web-inspection/src/', $path);
        }

        foreach ([Scanner::class, self::legacyClassFor(Scanner::class)] as $class) {
            foreach (['identify', 'runsLaravel', 'runsExpressJs', 'runsVueJs', 'runsNuxtJs', 'runsReactJs', 'runsAngular', 'hasBootstrap'] as $method) {
                self::assertTrue(method_exists($class, $method), $class . '::' . $method);
            }
        }

        foreach ([DomCrawler::class, self::legacyClassFor(DomCrawler::class)] as $class) {
            foreach (['get', 'exists', 'getAttr', 'getText', 'getHTML', 'getAll', 'getAttributes', 'getTexts', 'getLinks'] as $method) {
                self::assertTrue(method_exists($class, $method), $class . '::' . $method);
            }
        }
    }

    public function testCmsDetectorsRecognizeTheirKnownMarkers(): void
    {
        $fixtures = [
            [Magento::class, 'x-magento-init'],
            [PrestaShop::class, 'var prestashop = {}'],
            [Shopify::class, 'window.Shopify = {}'],
            [Squarespace::class, 'sqs-cart-quantity'],
            [Wix::class, 'https://static.wixstatic.com/'],
            [WordPress::class, '/wp-content/plugins/sample/plugin.js'],
        ];

        foreach ($fixtures as [$class, $marker]) {
            self::assertTrue($class::isIt($marker), $class);
            self::assertTrue(self::legacyClassFor($class)::isIt($marker), self::legacyClassFor($class));
        }
    }

    public function testDomCrawlerKeepsItsSelectorHelpers(): void
    {
        foreach ([DomCrawler::class, self::legacyClassFor(DomCrawler::class)] as $class) {
            $crawler = new $class('<html><body><a class="selected" href="/consumer">Consumer</a><a href="/next">Next</a></body></html>');

            self::assertTrue($crawler->exists('a.selected'));
            self::assertSame('/consumer', $crawler->getAttr('a.selected', 'href'));
            self::assertSame('Consumer', $crawler->getText('a.selected'));
            self::assertSame('Consumer', $crawler->getHTML('a.selected'));
            self::assertCount(2, $crawler->get('a'));
            self::assertCount(2, $crawler->getAll('a'));
            self::assertSame(['/consumer', '/next'], $crawler->getAttributes('a', 'href'));
            self::assertSame(['Consumer', 'Next'], $crawler->getTexts('a', 'href'));
            self::assertSame(['/consumer', '/next'], $crawler->getLinks());
        }
    }

    public function testScannerStaticClassificationNeedsNoNetworkCall(): void
    {
        $contentProperty = new ReflectionProperty(Scanner::class, 'content');
        $headersProperty = new ReflectionProperty(Scanner::class, 'headers');
        $oldContent = $contentProperty->getValue();
        $oldHeaders = $headersProperty->getValue();

        try {
            $contentProperty->setValue(null, '<meta name="csrf-token"> vue.global.js /react-dom@18 <app-root ng-version="18"> btn-primary');
            $headersProperty->setValue(null, ['server' => 'express']);

            foreach ([Scanner::class, self::legacyClassFor(Scanner::class)] as $class) {
                self::assertTrue($class::runsLaravel());
                self::assertTrue($class::runsExpressJs());
                self::assertTrue($class::runsVueJs());
                self::assertTrue($class::runsReactJs());
                self::assertTrue($class::runsAngular());
                self::assertTrue($class::hasBootstrap());
            }
        } finally {
            $contentProperty->setValue(null, $oldContent);
            $headersProperty->setValue(null, $oldHeaders);
        }
    }

    private static function legacyClassFor(string $implementation): string
    {
        $legacy = array_search($implementation, self::LEGACY_CLASSES, true);
        self::assertIsString($legacy);

        return $legacy;
    }
}
