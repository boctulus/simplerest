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
 * OpenFacturaControllerErrorTest
 * 
 * Error handling tests for OpenFacturaController
 */
class OpenFacturaControllerErrorTest extends TestCase
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
     * Test emitDTE method when SDK throws an exception
     */
    public function testEmitDTEWithSdkException()
    {
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
            ]
        ];

        // Create mock Request and Response objects
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('emitirDTE')
            ->willThrowException(new Exception('SDK Error: Failed to emit DTE'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow - this should handle the exception gracefully
        $reflection->getMethod('emitDTE')->invoke($controller);

        $this->assertTrue(true);
    }
    
    /**
     * Test getDTEStatus method when SDK throws an exception
     */
    public function testGetDTEStatusWithSdkException()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('getDTEStatus')
            ->willThrowException(new Exception('SDK Error: Failed to get DTE status'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow
        $result = $this->invokeMethod($controller, 'getDTEStatus', ['valid_token']);

        $this->assertTrue(true);
    }
    
    /**
     * Test anularGuiaDespacho method when SDK throws an exception
     */
    public function testAnularGuiaDespachoWithSdkException()
    {
        $requestBody = [
            'folio' => 12345,
            'fecha' => '2025-01-15'
        ];

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('anularGuiaDespacho')
            ->willThrowException(new Exception('SDK Error: Failed to cancel dispatch guide'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow
        $reflection->getMethod('anularGuiaDespacho')->invoke($controller);

        $this->assertTrue(true);
    }
    
    /**
     * Test anularDTE method when SDK throws an exception
     */
    public function testAnularDTEWithSdkException()
    {
        $requestBody = [
            'dteData' => [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 61, // Nota de Crédito
                    ],
                ],
            ]
        ];

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('emitirDTE') // Anular uses emitirDTE internally for credit notes
            ->willThrowException(new Exception('SDK Error: Failed to cancel DTE'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow
        $reflection->getMethod('anularDTE')->invoke($controller);

        $this->assertTrue(true);
    }
    
    /**
     * Test getTaxpayer method when SDK throws an exception
     */
    public function testGetTaxpayerWithSdkException()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('getTaxpayer')
            ->willThrowException(new Exception('SDK Error: Failed to get taxpayer info'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow
        $result = $this->invokeMethod($controller, 'getTaxpayer', ['76399751-9']);

        $this->assertTrue(true);
    }
    
    /**
     * Test getOrganization method when SDK throws an exception
     */
    public function testGetOrganizationWithSdkException()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('getOrganization')
            ->willThrowException(new Exception('SDK Error: Failed to get organization info'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow
        $reflection->getMethod('getOrganization')->invoke($controller);

        $this->assertTrue(true);
    }
    
    /**
     * Test getSalesRegistry method when SDK throws an exception
     */
    public function testGetSalesRegistryWithSdkException()
    {
        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRequest->expects($this->any())
            ->method('getHeaders')
            ->willReturn([]);

        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(500);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('getSalesRegistry')
            ->willThrowException(new Exception('SDK Error: Failed to get sales registry'));

        $sdkProperty->setValue($controller, $mockSdk);

        // Call the method to trigger the flow
        $result = $this->invokeMethod($controller, 'getSalesRegistry', ['2025', '01']);

        $this->assertTrue(true);
    }
    
    /**
     * Test health method when SDK throws an exception (simulating API unavailability)
     */
    public function testHealthWithSdkException()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('status')
            ->with(503); // Service Unavailable

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       !empty($data['error']);
            }));

        // Set singleton instances before creating the controller
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Access the SDK property and replace it with a mock that throws an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('checkApiStatus')
            ->willThrowException(new Exception('SDK Error: API is unavailable'));

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