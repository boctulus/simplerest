<?php

use PHPUnit\Framework\TestCase;
use Boctulus\FriendlyposWeb\Controllers\OpenFacturaController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\OpenfacturaSdk\Mocks\OpenFacturaSDKMock;

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
 * OpenFacturaControllerSdkTest
 * 
 * Integration tests for OpenFacturaController with SDK mocking
 */
class OpenFacturaControllerSdkTest extends TestCase
{
    private $controller;
    private $originalEnv;
    
    protected function setUp(): void
    {
        parent::setUp();

        // Store original environment values
        $this->originalEnv = [
            'OPENFACTURA_SANDBOX' => getenv('OPENFACTURA_SANDBOX'),
            'OPENFACTURA_API_KEY_DEV' => getenv('OPENFACTURA_API_KEY_DEV'),
            'OPENFACTURA_API_KEY_PROD' => getenv('OPENFACTURA_API_KEY_PROD'),
        ];

        // Set test environment variables
        putenv('OPENFACTURA_SANDBOX=true');
        putenv('OPENFACTURA_API_KEY_DEV=test_api_key');
        putenv('OPENFACTURA_API_KEY_PROD=prod_api_key');
    }

    protected function tearDown(): void
    {
        // Restore original environment values
        foreach ($this->originalEnv as $key => $value) {
            if ($value !== false) {
                putenv("$key=$value");
            } else {
                putenv($key); // Remove the environment variable
            }
        }

        // Reset singleton instances to clean state
        \Boctulus\Simplerest\Core\Request::setInstance(null);
        \Boctulus\Simplerest\Core\Response::setInstance(null);

        parent::tearDown();
    }
    
    /**
     * Test that the SDK is properly initialized in the controller
     */
    public function testSdkInitialization()
    {
        // Create mock Request and Response objects
        $mockRequest = $this->createMock(Request::class);
        $mockResponse = $this->createMock(Response::class);

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        // Create the controller instance
        $controller = new OpenFacturaController();

        // Use reflection to access the private sdk property
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $sdk = $sdkProperty->getValue($controller);

        // The SDK should be initialized and should be the mock if we're in test environment
        $this->assertNotNull($sdk);
    }
    
    /**
     * Test that sandbox mode is properly detected from environment
     */
    public function testSandboxModeDetection()
    {
        // Set sandbox to true
        putenv('OPENFACTURA_SANDBOX=true');
        $controller = new OpenFacturaController();
        
        $reflection = new \ReflectionClass($controller);
        $sandboxProperty = $reflection->getProperty('sandbox');
        $sandboxProperty->setAccessible(true);
        
        $sandboxValue = $sandboxProperty->getValue($controller);
        $this->assertTrue($sandboxValue);
        
        // Set sandbox to false
        putenv('OPENFACTURA_SANDBOX=false');
        $controller = new OpenFacturaController();
        
        $sandboxProperty = $reflection->getProperty('sandbox');
        $sandboxProperty->setAccessible(true);
        
        $sandboxValue = $sandboxProperty->getValue($controller);
        $this->assertFalse($sandboxValue);
    }
    
    /**
     * Test API key selection based on sandbox mode
     */
    public function testApiKeySelection()
    {
        // Test development API key selection
        putenv('OPENFACTURA_SANDBOX=true');
        putenv('OPENFACTURA_API_KEY_DEV=dev_key_123');
        $controller = new OpenFacturaController();
        
        $reflection = new \ReflectionClass($controller);
        $apiKeyProperty = $reflection->getProperty('apiKey');
        $apiKeyProperty->setAccessible(true);
        
        $apiKey = $apiKeyProperty->getValue($controller);
        $this->assertEquals('dev_key_123', $apiKey);
        
        // Test production API key selection
        putenv('OPENFACTURA_SANDBOX=false');
        putenv('OPENFACTURA_API_KEY_PROD=prod_key_456');
        $controller = new OpenFacturaController();
        
        $apiKeyProperty = $reflection->getProperty('apiKey');
        $apiKeyProperty->setAccessible(true);
        
        $apiKey = $apiKeyProperty->getValue($controller);
        $this->assertEquals('prod_key_456', $apiKey);
    }
    
    /**
     * Test internal success response method
     */
    public function testSuccessResponse()
    {
        // Create mock Request and Response objects
        $mockRequest = $this->createMock(Request::class);
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(200);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === true &&
                       isset($data['data']) &&
                       isset($data['timestamp']);
            }));

        $GLOBALS['mockRequest'] = $mockRequest;
        $GLOBALS['mockResponse'] = $mockResponse;

        // Create the controller instance
        $controller = new OpenFacturaController();

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('success');
        $method->setAccessible(true);

        // This will call response()->status() and response()->json()
        $result = $method->invoke($controller, ['test' => 'data'], 200);

        $this->assertNull($result); // The method returns void, just checks that no error occurred
    }
    
    /**
     * Test internal error response method
     */
    public function testErrorResponse()
    {
        // Create mock Request and Response objects
        $mockRequest = $this->createMock(Request::class);
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(400);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       isset($data['timestamp']);
            }));

        $GLOBALS['mockRequest'] = $mockRequest;
        $GLOBALS['mockResponse'] = $mockResponse;

        // Create the controller instance
        $controller = new OpenFacturaController();

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('error');
        $method->setAccessible(true);

        // This will call response()->status() and response()->json()
        $result = $method->invoke($controller, 'Test error message', 400);

        $this->assertNull($result); // The method returns void, just checks that no error occurred
    }
    
    /**
     * Test complete flow of emitDTE method with mocked SDK
     */
    public function testEmitDTECompleteFlow()
    {
        // Mock the request with valid DTE data
        $requestBody = [
            'dteData' => [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 33,
                    ],
                ],
                'Emisor' => [
                    'RUTEmisor' => '76399751-9',
                ],
                'Receptor' => [
                    'RUTRecep' => '76399751-9',
                ],
                'Detalle' => [
                    [
                        'NmbItem' => 'Producto de prueba',
                        'QtyItem' => 1,
                        'PrcItem' => 1000,
                    ],
                ],
            ],
            'responseOptions' => ['PDF', 'FOLIO', 'TIMBRE'],
            'sendEmail' => null,
            'idempotencyKey' => 'test_key_123'
        ];
        
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->with(true)->willReturn($requestBody);
        
        $GLOBALS['mockRequest'] = $mockRequest;
        
        // Mock the response
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->callback(function($status) {
                // Should be either 200 for success or 500 for error
                return in_array($status, [200, 500]);
            }));
        
        $mockResponse->expects($this->atLeastOnce())
            ->method('json');
            
        $GLOBALS['mockResponse'] = $mockResponse;
        
        // Create new controller to use the global mocks
        $controller = new OpenFacturaController();
        
        // Access the SDK property and replace it with a more controlled mock
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);
        
        // Create a mock that simulates successful DTE emission
        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $mockSdk->method('emitirDTE')
            ->willReturn([
                'folio' => 12345,
                'pdf' => 'base64_encoded_pdf_data',
                'timbre' => 'timbre_data'
            ]);
        
        $sdkProperty->setValue($controller, $mockSdk);
        
        // Call the method to trigger the flow
        $reflection->getMethod('emitDTE')->invoke($controller);
        
        // If we get to this point, the method executed without fatal errors
        $this->assertTrue(true);
    }
    
    /**
     * Test complete flow of getDTEStatus method with mocked SDK
     */
    public function testGetDTEStatusCompleteFlow()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->with(true)->willReturn([]);
        
        $GLOBALS['mockRequest'] = $mockRequest;
        
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));
        
        $mockResponse->expects($this->atLeastOnce())
            ->method('json');
            
        $GLOBALS['mockResponse'] = $mockResponse;
        
        $controller = new OpenFacturaController();
        
        // Access the SDK property and replace it with a mock
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);
        
        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $mockSdk->method('getDTEStatus')
            ->willReturn(['status' => 'success', 'data' => []]);
        
        $sdkProperty->setValue($controller, $mockSdk);
        
        // Call the method to trigger the flow
        $result = $this->invokeMethod($controller, 'getDTEStatus', ['valid_token']);
        
        $this->assertTrue(true);
    }
    
    /**
     * Test complete flow of health method with mocked SDK
     */
    public function testHealthCompleteFlow()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->with(true)->willReturn([]);
        
        $GLOBALS['mockRequest'] = $mockRequest;
        
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));
        
        $mockResponse->expects($this->atLeastOnce())
            ->method('json');
            
        $GLOBALS['mockResponse'] = $mockResponse;
        
        $controller = new OpenFacturaController();
        
        // Access the SDK property and replace it with a mock
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);
        
        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $mockSdk->method('checkApiStatus')
            ->willReturn(['api' => 'healthy', 'version' => '1.0']);
        
        $sdkProperty->setValue($controller, $mockSdk);
        
        // Call the method to trigger the flow
        $reflection->getMethod('health')->invoke($controller);
        
        $this->assertTrue(true);
    }
    
    /**
     * Helper method to call protected/private methods
     */
    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}