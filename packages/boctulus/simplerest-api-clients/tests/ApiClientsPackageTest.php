<?php

declare(strict_types=1);

use Boctulus\Simplerest\Integrations\Libs\GoogleDrive;
use Boctulus\Simplerest\Integrations\Libs\HuggingFace;
use Boctulus\Simplerest\Integrations\Libs\OneSignal;
use Boctulus\Simplerest\Integrations\Libs\WooCommerce;
use Boctulus\Simplerest\Integrations\Libs\WooCommerceApiClient;
use PHPUnit\Framework\TestCase;

final class ApiClientsPackageTest extends TestCase
{
    /** @var array<string, string> */
    private const LEGACY_CLASSES = [
        'Boctulus\\Simplerest\\Core\\Libs\\GoogleDrive' => GoogleDrive::class,
        'Boctulus\\Simplerest\\Core\\Libs\\HuggingFace' => HuggingFace::class,
        'Boctulus\\Simplerest\\Core\\Libs\\OneSignal' => OneSignal::class,
        'Boctulus\\Simplerest\\Core\\Libs\\WooCommerce' => WooCommerce::class,
        'Boctulus\\Simplerest\\Core\\Libs\\WooCommerceApiClient' => WooCommerceApiClient::class,
    ];

    public function testApiClientsKeepTheirLegacyNamesAndPublicMethods(): void
    {
        foreach (self::LEGACY_CLASSES as $legacy => $implementation) {
            self::assertTrue(class_exists($implementation), $implementation);
            self::assertTrue(class_exists($legacy), $legacy);
            $path = str_replace('\\', '/', (string) (new ReflectionClass($implementation))->getFileName());
            self::assertStringContainsString('/simplerest-api-clients/src/', $path);
        }

        self::assertTrue(method_exists(GoogleDrive::class, 'getFolderInfo'));
        self::assertTrue(method_exists(HuggingFace::class, 'exec'));
        self::assertTrue(method_exists(OneSignal::class, 'send'));
        self::assertTrue(method_exists(WooCommerce::class, 'transformProductData'));
        self::assertTrue(method_exists(WooCommerceApiClient::class, 'setOAuth'));
    }

    public function testWooCommerceProductTransformIsIndependentOfHttp(): void
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

    public function testOneSignalEmptyPayloadDoesNotSendARequest(): void
    {
        self::assertNull(OneSignal::send([]));
        self::assertNull(self::legacyClassFor(OneSignal::class)::send([]));
    }

    public function testHuggingFaceRejectsUnknownModelsBeforeMakingARequest(): void
    {
        foreach ([HuggingFace::class, self::legacyClassFor(HuggingFace::class)] as $class) {
            $client = new $class('test-key');
            try {
                $client->exec('not-a-supported-model');
                self::fail('Unknown Hugging Face models should be rejected before an HTTP request.');
            } catch (Exception $exception) {
                self::assertStringContainsString('Model not found', $exception->getMessage());
            }
        }
    }

    public function testHuggingFaceMissingApiKeyThrowsAnExceptionInsteadOfTerminatingTheProcess(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('huggingface_api_key is required');

        new HuggingFace('');
    }

    public function testGoogleDriveReportsItsOptionalSdkRequirement(): void
    {
        if (class_exists('Google\\Client')) {
            self::markTestSkipped('The optional Google API client is installed in this consumer.');
        }

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Install the optional Google Drive SDK');
        (new GoogleDrive())->getFolderInfo();
    }

    private static function legacyClassFor(string $implementation): string
    {
        $legacy = array_search($implementation, self::LEGACY_CLASSES, true);
        self::assertIsString($legacy);

        return $legacy;
    }
}
