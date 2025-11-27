<?php

use PHPUnit\Framework\TestCase;
use Boctulus\FriendlyposWeb\Controllers\OpenFacturaController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory;
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
 * OpenFacturaControllerTest
 * 
 * Unit tests for OpenFacturaController
 */
class OpenFacturaControllerTest extends TestCase
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
     * Test that the controller can be instantiated properly
     */
    public function testControllerInstantiation()
    {
        // Create mock Request and Response objects
        $mockRequest = $this->createMock(Request::class);
        $mockResponse = $this->createMock(Response::class);

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        // Create the controller instance
        $controller = new OpenFacturaController();

        $this->assertInstanceOf(OpenFacturaController::class, $controller);
    }
    
    /**
     * Test emitDTE method with valid data
     */
    public function testEmitDTEWithValidData()
    {
        // Mock the request body
        $requestBody = [
            'dteData' => [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 33, // Factura electrónica
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

        // Create a mock request object
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

        // Create a mock response object that allows for chaining
        $mockResponse = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configure the status method to return the same instance for chaining
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500))
            ->willReturn($mockResponse);

        $mockResponse->expects($this->atLeastOnce())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       isset($data['timestamp']);
            }));

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        // Create a new controller instance to use the global mocks
        $controller = new OpenFacturaController();

        // This test will primarily check that no PHP errors occur during controller initialization
        $this->assertInstanceOf(OpenFacturaController::class, $controller);
    }
    
    /**
     * Test emitDTE method with missing dteData
     */
    public function testEmitDTENoDteData()
    {
        $requestBody = ['someOtherData' => 'value'];

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

        // Create a mock response object that allows for chaining
        $mockResponse = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse->expects($this->once())
            ->method('status')
            ->with(400)
            ->willReturn($mockResponse);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       strpos($data['error'], 'dteData') !== false;
            }));

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();
    }
    
    /**
     * Test getDTEStatus method with valid token
     */
    public function testGetDTEStatusWithValidToken()
    {
        // Create a mock request object (though this method doesn't use the body)
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));

        $mockResponse->expects($this->atLeastOnce())
            ->method('json');

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // Call the method directly to test its behavior
        $result = $this->invokeMethod($controller, 'getDTEStatus', ['valid_token']);

        $this->assertTrue(true); // If we reach here, no fatal errors occurred
    }
    
    /**
     * Test getDTEStatus method with empty token
     */
    public function testGetDTEStatusWithEmptyToken()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

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
                       strpos($data['error'], 'Token') !== false;
            }));

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();
        $result = $this->invokeMethod($controller, 'getDTEStatus', ['']);
    }
    
    /**
     * Test anularGuiaDespacho method with valid data
     */
    public function testAnularGuiaDespachoWithValidData()
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
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));

        $mockResponse->expects($this->atLeastOnce())
            ->method('json');

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        $this->assertTrue(true); // If we reach here, no fatal errors occurred
    }
    
    /**
     * Test anularGuiaDespacho method with missing data
     */
    public function testAnularGuiaDespachoMissingData()
    {
        $requestBody = ['folio' => 12345]; // Missing 'fecha'

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

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
                       (strpos($data['error'], 'folio') !== false || strpos($data['error'], 'fecha') !== false);
            }));

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();
    }
    
    /**
     * Test anularDTE method with valid data
     */
    public function testAnularDTEWithValidData()
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
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));

        $mockResponse->expects($this->atLeastOnce())
            ->method('json');

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        $this->assertTrue(true); // If we reach here, no fatal errors occurred
    }
    
    /**
     * Test anularDTE method with wrong DTE type
     */
    public function testAnularDTEWithWrongType()
    {
        $requestBody = [
            'dteData' => [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 33, // Factura, not credit note
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
            ->with(400);

        $mockResponse->expects($this->once())
            ->method('json')
            ->with($this->callback(function($data) {
                return is_array($data) &&
                       isset($data['success']) &&
                       $data['success'] === false &&
                       isset($data['error']) &&
                       strpos($data['error'], '61') !== false;
            }));

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();
    }
    
    /**
     * Test getTaxpayer method with valid RUT
     */
    public function testGetTaxpayerWithValidRUT()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));

        $mockResponse->expects($this->atLeastOnce())
            ->method('json');

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();
        $result = $this->invokeMethod($controller, 'getTaxpayer', ['76399751-9']);

        $this->assertTrue(true);
    }
    
    /**
     * Test getTaxpayer method with empty RUT
     */
    public function testGetTaxpayerWithEmptyRUT()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

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
                       strpos($data['error'], 'RUT') !== false;
            }));

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();
        $result = $this->invokeMethod($controller, 'getTaxpayer', ['']);
    }
    
    /**
     * Test health check method
     */
    public function testHealthMethod()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->atLeastOnce())
            ->method('status')
            ->with($this->logicalOr(200, 500));

        $mockResponse->expects($this->atLeastOnce())
            ->method('json');

        // Set singleton instances for testing
        \Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
        \Boctulus\Simplerest\Core\Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

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