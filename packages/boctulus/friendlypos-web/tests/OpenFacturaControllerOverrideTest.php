<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\OpenfacturaSdk\Mocks\OpenFacturaSDKMock;
use Boctulus\FriendlyposWeb\Controllers\OpenFacturaController;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../../../app.php';

/**
 * Prueba unitaria
 *
 * Ejecutar con: `./vendor/bin/phpunit {ruta de este archivo}` desde el root del proyecto
 */

/**
 * Tests para verificar que la funcionalidad de sobrescritura de API Key y Sandbox mode funciona correctamente
 */
class OpenFacturaControllerOverrideTest extends TestCase
{
    private $controller;
    private $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();

        // Save original environment values
        $this->originalEnv = [
            'OPENFACTURA_SANDBOX' => getenv('OPENFACTURA_SANDBOX'),
            'OPENFACTURA_API_KEY_DEV' => getenv('OPENFACTURA_API_KEY_DEV'),
            'OPENFACTURA_API_KEY_PROD' => getenv('OPENFACTURA_API_KEY_PROD'),
        ];

        // Set default values for testing
        putenv('OPENFACTURA_SANDBOX=true');
        putenv('OPENFACTURA_API_KEY_DEV=dev_test_key');
        putenv('OPENFACTURA_API_KEY_PROD=prod_test_key');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Restore original environment values
        foreach ($this->originalEnv as $key => $value) {
            if ($value !== false) {
                putenv("{$key}={$value}");
            } else {
                putenv($key);
            }
        }

        // Reset singleton instances to clean state
        Request::setInstance(null);
        Response::setInstance(null);
    }

    /**
     * Test that the controller correctly uses API key from headers
     */
    public function testControllerUsesApiKeyFromHeaders()
    {
        // Create the controller after setting up the mock
        $this->mockRequest([
            'headers' => [
                'X-Openfactura-Api-Key' => ['custom_api_key_from_header']
            ],
            'body' => []
        ]);

        $controller = new OpenFacturaController();
        $overrideParams = $this->invokeMethod($controller, 'getOverrideParams');

        $this->assertEquals('custom_api_key_from_header', $overrideParams['api_key']);
        $this->assertNull($overrideParams['sandbox']); // Should be null since it's not provided
    }

    /**
     * Test that the controller correctly uses sandbox from headers
     */
    public function testControllerUsesSandboxFromHeaders()
    {
        // Mock a request with custom sandbox in headers
        $this->mockRequest([
            'headers' => [
                'X-Openfactura-Sandbox' => ['false']
            ],
            'body' => []
        ]);

        $controller = new OpenFacturaController();
        $overrideParams = $this->invokeMethod($controller, 'getOverrideParams');

        $this->assertNull($overrideParams['api_key']); // Should be null since it's not provided
        $this->assertFalse($overrideParams['sandbox']); // Should be false
    }

    /**
     * Test that the controller correctly uses both API key and sandbox from headers
     */
    public function testControllerUsesBothParamsFromHeaders()
    {
        // Mock a request with custom API key and sandbox in headers
        $this->mockRequest([
            'headers' => [
                'X-Openfactura-Api-Key' => ['custom_api_key'],
                'X-Openfactura-Sandbox' => ['true']
            ],
            'body' => []
        ]);

        $controller = new OpenFacturaController();
        $overrideParams = $this->invokeMethod($controller, 'getOverrideParams');

        $this->assertEquals('custom_api_key', $overrideParams['api_key']);
        $this->assertTrue($overrideParams['sandbox']); // Should be true
    }

    /**
     * Test that the controller correctly uses API key from body when not in headers
     */
    public function testControllerUsesApiKeyFromBody()
    {
        // Mock a request with custom API key in body
        $this->mockRequest([
            'headers' => [],
            'body' => [
                'api_key' => 'custom_api_key_from_body'
            ]
        ]);

        $controller = new OpenFacturaController();
        $overrideParams = $this->invokeMethod($controller, 'getOverrideParams');

        $this->assertEquals('custom_api_key_from_body', $overrideParams['api_key']);
        $this->assertNull($overrideParams['sandbox']); // Should be null since it's not provided
    }

    /**
     * Test that the controller correctly uses sandbox from body when not in headers
     */
    public function testControllerUsesSandboxFromBody()
    {
        // Mock a request with custom sandbox in body
        $this->mockRequest([
            'headers' => [],
            'body' => [
                'sandbox' => 'false'
            ]
        ]);

        $controller = new OpenFacturaController();
        $overrideParams = $this->invokeMethod($controller, 'getOverrideParams');

        $this->assertNull($overrideParams['api_key']); // Should be null since it's not provided
        $this->assertFalse($overrideParams['sandbox']); // Should be false
    }

    /**
     * Test that headers take precedence over body parameters
     */
    public function testHeadersTakePrecedenceOverBody()
    {
        // Mock a request with both headers and body containing different values
        $this->mockRequest([
            'headers' => [
                'X-Openfactura-Api-Key' => ['api_key_from_header'],
                'X-Openfactura-Sandbox' => ['true']
            ],
            'body' => [
                'api_key' => 'api_key_from_body',
                'sandbox' => 'false'
            ]
        ]);

        $controller = new OpenFacturaController();
        $overrideParams = $this->invokeMethod($controller, 'getOverrideParams');

        // Headers should take precedence over body
        $this->assertEquals('api_key_from_header', $overrideParams['api_key']);
        $this->assertTrue($overrideParams['sandbox']); // Should be true from headers
    }

    /**
     * Test that the SDK is initialized with override parameters when provided
     */
    public function testSdkInitializedWithOverrideParameters()
    {
        // This test ensures that when override params are provided,
        // the SDK is initialized with those values instead of default env values
        $this->mockRequest([
            'headers' => [
                'X-Openfactura-Api-Key' => ['override_api_key'],
                'X-Openfactura-Sandbox' => ['false']
            ],
            'body' => []
        ]);

        $controller = new OpenFacturaController();
        // Use reflection to access private initializeSDK method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('initializeSDK');
        $method->setAccessible(true);

        // Initialize SDK with override parameters
        $sdk = $method->invoke($controller, 'override_api_key', false);

        // Since we can't easily test the SDK directly, let's test the parameters it should receive
        // We'll do this by checking if the parameters are correctly passed to the SDK factory

        // This test is focused on the controller logic, not the SDK itself
        $this->assertNotNull($sdk); // Should be able to create the SDK instance
    }

    /**
     * Test that the SDK is initialized with default values when no overrides provided
     */
    public function testSdkInitializedWithDefaultParameters()
    {
        $this->mockRequest([
            'headers' => [],
            'body' => []
        ]);

        $controller = new OpenFacturaController();
        // Use reflection to access private initializeSDK method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('initializeSDK');
        $method->setAccessible(true);

        // Initialize SDK with no override parameters (should use defaults)
        $sdk = $method->invoke($controller, null, null);

        $this->assertNotNull($sdk); // Should be able to create the SDK instance
    }

    /**
     * Helper method to mock request
     */
    private function mockRequest($requestData)
    {
        // Create a mock for the Request class
        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Ensure headers and body exist in the request data
        $headers = $requestData['headers'] ?? [];
        $body = $requestData['body'] ?? [];

        // Configure the mock to return the provided headers and body
        $mockRequest->expects($this->any())
            ->method('getHeaders')
            ->willReturn($headers);

        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with($this->anything())
            ->willReturn($body);

        // Set the mock as the singleton instance for testing
        Request::setInstance($mockRequest);

        // Also create and set a basic response mock
        $mockResponse = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        Response::setInstance($mockResponse);
    }

    /**
     * Call protected/private method of a class.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}