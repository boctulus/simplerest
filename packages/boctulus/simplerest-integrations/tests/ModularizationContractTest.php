<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Integrations\Tests;

use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Integrations\Libs\BootstrapPaginator;
use Boctulus\Simplerest\Integrations\Libs\CMS_Scanner\Scanner;
use Boctulus\Simplerest\Integrations\Libs\DomCrawler;
use Boctulus\Simplerest\Integrations\Libs\HuggingFace;
use Boctulus\Simplerest\Integrations\Libs\OneSignal;
use Boctulus\Simplerest\Integrations\Libs\WooCommerce;
use Boctulus\Simplerest\Integrations\Libs\WooCommerceApiClient;
use PHPUnit\Framework\TestCase;

final class ModularizationContractTest extends TestCase
{
    /** @var array<string, string> */
    private const LEGACY_TO_IMPLEMENTATION = [
        'Boctulus\\Simplerest\\Core\\Libs\\BootstrapPaginator' => 'Boctulus\\Simplerest\\Integrations\\Libs\\BootstrapPaginator',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\Scanner' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\Scanner',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Magento' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Magento',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\PrestaShop' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\PrestaShop',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Shopify' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Shopify',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Squarespace' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Squarespace',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\Wix' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Wix',
        'Boctulus\\Simplerest\\Core\\Libs\\CMS_Scanner\\CMSs\\WordPress' => 'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\WordPress',
        'Boctulus\\Simplerest\\Core\\Libs\\DomCrawler' => 'Boctulus\\Simplerest\\Integrations\\Libs\\DomCrawler',
        'Boctulus\\Simplerest\\Core\\Libs\\GoogleDrive' => 'Boctulus\\Simplerest\\Integrations\\Libs\\GoogleDrive',
        'Boctulus\\Simplerest\\Core\\Libs\\HuggingFace' => 'Boctulus\\Simplerest\\Integrations\\Libs\\HuggingFace',
        'Boctulus\\Simplerest\\Core\\Libs\\OneSignal' => 'Boctulus\\Simplerest\\Integrations\\Libs\\OneSignal',
        'Boctulus\\Simplerest\\Core\\Libs\\WooCommerce' => 'Boctulus\\Simplerest\\Integrations\\Libs\\WooCommerce',
        'Boctulus\\Simplerest\\Core\\Libs\\WooCommerceApiClient' => 'Boctulus\\Simplerest\\Integrations\\Libs\\WooCommerceApiClient',
    ];

    /** @var array<string, list<string>> */
    private const CONTRACT_METHODS = [
        'Boctulus\\Simplerest\\Integrations\\Libs\\BootstrapPaginator' => ['render'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\Scanner' => ['identify', 'runsLaravel', 'runsExpressJs', 'runsVueJs', 'runsNuxtJs', 'runsReactJs', 'runsAngular', 'hasBootstrap'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Magento' => ['isIt'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\PrestaShop' => ['isIt'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Shopify' => ['isIt'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Squarespace' => ['isIt'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Wix' => ['isIt'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\WordPress' => ['isIt'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\DomCrawler' => ['get', 'exists', 'getAttr', 'getText', 'getHTML', 'getAll', 'getAttributes', 'getTexts', 'getLinks'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\GoogleDrive' => ['getFolderInfo', 'getInfo', 'getUpdateDate', 'download'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\HuggingFace' => ['addInput', 'setParams', 'exec'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\OneSignal' => ['getClient', 'send', 'addDevice', 'app', 'getUsers', 'getNotifications'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\WooCommerce' => ['createProduct', 'createVariation', 'transformProductData'],
        'Boctulus\\Simplerest\\Integrations\\Libs\\WooCommerceApiClient' => ['setOAuth'],
    ];

    public function test_all_fourteen_implementations_and_legacy_names_autoload(): void
    {
        self::assertCount(14, self::LEGACY_TO_IMPLEMENTATION);

        foreach (self::LEGACY_TO_IMPLEMENTATION as $legacyClass => $implementationClass) {
            self::assertTrue(class_exists($implementationClass), $implementationClass);
            self::assertTrue(class_exists($legacyClass), $legacyClass);

            $implementation = new \ReflectionClass($implementationClass);
            $implementationPath = str_replace('\\', '/', (string) $implementation->getFileName());
            self::assertStringContainsString('/simplerest-integrations/src/', $implementationPath);

            foreach (self::CONTRACT_METHODS[$implementationClass] as $method) {
                self::assertTrue(method_exists($implementationClass, $method), $implementationClass . '::' . $method);
                self::assertTrue(method_exists($legacyClass, $method), $legacyClass . '::' . $method);
            }

            $legacy = new \ReflectionClass($legacyClass);
            if ($legacy->getName() !== $legacyClass) {
                self::assertSame($implementationClass, $legacy->getName(), $legacyClass);
                self::assertSame($implementation->getFileName(), $legacy->getFileName(), $legacyClass);
            }
        }
    }

    public function test_all_six_cms_detectors_keep_their_recognition_contracts(): void
    {
        $fixtures = [
            ['Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Magento', 'x-magento-init'],
            ['Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\PrestaShop', 'var prestashop = {}'],
            ['Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Shopify', 'window.Shopify = {}'],
            ['Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Squarespace', 'sqs-cart-quantity'],
            ['Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\Wix', 'https://static.wixstatic.com/'],
            ['Boctulus\\Simplerest\\Integrations\\Libs\\CMS_Scanner\\CMSs\\WordPress', '/wp-content/plugins/sample/plugin.js'],
        ];

        foreach ($fixtures as [$class, $marker]) {
            $legacyClass = self::legacyClassFor($class);
            self::assertTrue($class::isIt($marker), $class);
            self::assertTrue($legacyClass::isIt($marker), $legacyClass);
        }
    }

    public function test_scanner_static_classification_helpers_need_no_network(): void
    {
        $content = '<meta name="csrf-token"> vue.global.js /react-dom@18 <app-root ng-version="18"> btn-primary';
        foreach ([Scanner::class, self::legacyClassFor(Scanner::class)] as $class) {
            $contentProperty = new \ReflectionProperty($class, 'content');
            $headersProperty = new \ReflectionProperty($class, 'headers');
            $oldContent = $contentProperty->getValue();
            $oldHeaders = $headersProperty->getValue();

            try {
                $contentProperty->setValue(null, $content);
                $headersProperty->setValue(null, ['server' => 'express']);

                self::assertTrue($class::runsLaravel(), $class);
                self::assertTrue($class::runsExpressJs(), $class);
                self::assertTrue($class::runsVueJs(), $class);
                self::assertTrue($class::runsReactJs(), $class);
                self::assertTrue($class::runsAngular(), $class);
                self::assertTrue($class::hasBootstrap(), $class);
            } finally {
                $contentProperty->setValue(null, $oldContent);
                $headersProperty->setValue(null, $oldHeaders);
            }
        }
    }

    public function test_dom_crawler_preserves_the_complete_helper_surface(): void
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

    public function test_bootstrap_paginator_renders_without_loading_application_config(): void
    {
        $configProperty = new \ReflectionProperty(Config::class, 'data');
        $oldConfig = $configProperty->getValue();
        $configProperty->setValue(null, ['paginator' => []]);

        try {
            foreach ([BootstrapPaginator::class, self::legacyClassFor(BootstrapPaginator::class)] as $class) {
                $bufferLevel = ob_get_level();
                ob_start();
                try {
                    $class::render([
                        'paginator' => [
                            'last_page' => 3,
                            'current_page' => 2,
                        ],
                    ]);
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

    public function test_http_clients_keep_local_contracts_without_sending_requests(): void
    {
        foreach ([OneSignal::class, self::legacyClassFor(OneSignal::class)] as $class) {
            self::assertNull($class::send([]));
        }

        foreach ([WooCommerce::class, self::legacyClassFor(WooCommerce::class)] as $class) {
            $wooCommerce = new $class([
                'base_url' => 'https://example.invalid',
                'consumer_key' => 'test-key',
                'consumer_secret' => 'test-secret',
            ]);
            self::assertSame('Example', $wooCommerce->transformProductData([
                'type' => 'simple',
                'name' => 'Example',
                'sku' => 'EX-1',
                'description' => 'Fixture product',
                'categories' => [],
                'attributes' => [],
            ])['name']);
        }

        foreach ([WooCommerceApiClient::class, self::legacyClassFor(WooCommerceApiClient::class)] as $class) {
            $wooCommerceClient = new $class('test-key', 'test-secret');
            $urlProperty = new \ReflectionProperty(ApiClient::class, 'url');
            $verbProperty = new \ReflectionProperty(ApiClient::class, 'verb');
            $urlProperty->setValue($wooCommerceClient, 'https://example.invalid/wp-json/wc/v3/products');
            $verbProperty->setValue($wooCommerceClient, 'POST');
            self::assertSame($wooCommerceClient, $wooCommerceClient->setOAuth());
        }

        foreach ([HuggingFace::class, self::legacyClassFor(HuggingFace::class)] as $class) {
            $huggingFace = new $class('test-key');
            try {
                $huggingFace->exec('not-a-supported-model');
                self::fail('Unknown Hugging Face models should be rejected before an HTTP request.');
            } catch (\Exception $exception) {
                self::assertStringContainsString('Model not found', $exception->getMessage());
            }
        }
    }

    public function test_google_drive_reports_missing_optional_sdk_for_both_names(): void
    {
        if (class_exists('Google\\Client')) {
            self::markTestSkipped('The optional Google API client is installed in this consumer.');
        }

        foreach ([
            'Boctulus\\Simplerest\\Integrations\\Libs\\GoogleDrive',
            self::legacyClassFor('Boctulus\\Simplerest\\Integrations\\Libs\\GoogleDrive'),
        ] as $class) {
            try {
                (new $class())->getFolderInfo();
                self::fail('Google Drive should require its optional SDK when used.');
            } catch (\LogicException $exception) {
                self::assertStringContainsString('google/apiclient', $exception->getMessage());
            }
        }
    }

    private static function legacyClassFor(string $implementationClass): string
    {
        $legacyClass = array_search($implementationClass, self::LEGACY_TO_IMPLEMENTATION, true);
        self::assertIsString($legacyClass);

        return $legacyClass;
    }
}
