<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Integrations\Tests;

use Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\Shopify;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\CMSs\WordPress;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\Scanner;
use Boctulus\Simplerest\Integrations\Libs\DomCrawler;
use Boctulus\Simplerest\Integrations\Libs\GoogleDrive;
use Boctulus\Simplerest\Integrations\Libs\HuggingFace;
use Boctulus\Simplerest\Integrations\Libs\OneSignal;
use Boctulus\Simplerest\Integrations\Libs\WooCommerce;
use PHPUnit\Framework\TestCase;

final class IntegrationsPackageTest extends TestCase
{
    public function test_dom_crawler_exposes_the_scraping_helpers(): void
    {
        $crawler = new DomCrawler('<html><body><a class="selected" href="/consumer">Consumer</a></body></html>');

        self::assertTrue($crawler->exists('a.selected'));
        self::assertSame('/consumer', $crawler->getAttr('a.selected', 'href'));
        self::assertSame('Consumer', $crawler->getText('a.selected'));
        self::assertSame(['/consumer'], $crawler->getLinks());
    }

    public function test_cms_detectors_recognize_known_platform_markers(): void
    {
        self::assertTrue(WordPress::isIt('<script src="/wp-content/plugins/sample/plugin.js"></script>'));
        self::assertTrue(Shopify::isIt('<script>Shopify.shop = "example.myshopify.com";</script>'));
    }

    public function test_woocommerce_product_transform_is_independent_of_http(): void
    {
        $client = new WooCommerce([
            'base_url' => 'https://example.invalid',
            'consumer_key' => 'test-key',
            'consumer_secret' => 'test-secret',
        ]);

        $product = $client->transformProductData([
            'type' => 'simple',
            'name' => 'Example',
            'sku' => 'EX-1',
            'description' => 'Fixture product',
            'categories' => ['Sample'],
            'attributes' => ['Color' => ['Blue', 'Red']],
        ]);

        self::assertSame('Example', $product['name']);
        self::assertSame([['name' => 'Sample']], $product['categories']);
        self::assertSame([[
            'name' => 'Color',
            'visible' => true,
            'variation' => true,
            'options' => ['Blue', 'Red'],
        ]], $product['attributes']);
    }

    public function test_optional_clients_and_helpers_autoload_without_network_calls(): void
    {
        foreach ([GoogleDrive::class, HuggingFace::class, OneSignal::class, Scanner::class, BootstrapPaginator::class] as $class) {
            self::assertTrue(class_exists($class), $class);
        }
    }

    public function test_google_drive_reports_its_optional_sdk_dependency(): void
    {
        if (class_exists('Google\\Client')) {
            self::markTestSkipped('The optional Google API client is installed in this consumer.');
        }

        try {
            (new GoogleDrive())->getFolderInfo();
            self::fail('GoogleDrive should require google/apiclient when the feature is used.');
        } catch (\LogicException $exception) {
            self::assertStringContainsString('Install the optional Google Drive SDK', $exception->getMessage());
        }
    }
}
