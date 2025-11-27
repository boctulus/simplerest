# UNIT TESTS

Para poder tener acceso a todo el framework incluidas constantes (definidas en constants.php) asi como helpers y demas es fundamental el siguiente boostrapping en el archivo del script de las pruebas unitarias:

```php
<?php

use PHPUnit\Framework\TestCase;
// otros "use"(s)

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../../../app.php';

/*
	Ejecutar con: `./vendor/bin/phpunit {ruta de este archivo}` desde el root del proyecto
*/

//
// Resto del archivo con las pruebas unitarias
//
```

## Mocking de Request y Response

El framework SimpleRest utiliza el patrón Singleton para `Request` y `Response`. Para realizar mocking correcto en tests unitarios, es **fundamental** seguir estos principios:

### ✅ Configuración Correcta de Mocks

#### 1. Crear mocks con la sintaxis correcta de PHPUnit

**❌ INCORRECTO:**
```php
$mockRequest->method('getBody')->with(true)->willReturn($data);
```

**✅ CORRECTO:**
```php
$mockRequest->expects($this->any())
    ->method('getBody')
    ->with(true)
    ->willReturn($data);
```

#### 2. Inyectar mocks usando setInstance()

**❌ INCORRECTO:**
```php
$GLOBALS['mockRequest'] = $mockRequest;
$GLOBALS['mockResponse'] = $mockResponse;
```

**✅ CORRECTO:**
```php
\Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
\Boctulus\Simplerest\Core\Response::setInstance($mockResponse);
```

#### 3. Limpiar singletons en tearDown()

```php
protected function tearDown(): void
{
    // Reset singleton instances to clean state
    \Boctulus\Simplerest\Core\Request::setInstance(null);
    \Boctulus\Simplerest\Core\Response::setInstance(null);

    parent::tearDown();
}
```

### Ejemplo Completo

```php
public function testEmitDTEWithSdkException()
{
    $requestBody = [
        'dteData' => [
            'Encabezado' => [
                'IdDoc' => ['TipoDTE' => 33],
            ],
        ]
    ];

    // Create mock Request object
    $mockRequest = $this->createMock(Request::class);
    $mockRequest->expects($this->any())
        ->method('getBody')
        ->with(true)
        ->willReturn($requestBody);

    // Create mock Response object
    $mockResponse = $this->createMock(Response::class);
    $mockResponse->expects($this->once())
        ->method('status')
        ->with(500);

    $mockResponse->expects($this->once())
        ->method('json')
        ->with($this->callback(function($data) {
            return is_array($data) &&
                   isset($data['success']) &&
                   $data['success'] === false;
        }));

    // Set singleton instances before creating the controller
    Request::setInstance($mockRequest);
    Response::setInstance($mockResponse);

    $controller = new OpenFacturaController();

    // Invoke the method being tested
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('emitDTE');
    $method->invoke($controller);
}
```

### Notas Importantes

1. **Siempre usar `setInstance()`** para inyectar mocks en el Singleton
2. **Usar `expects()->method()->with()->willReturn()`** para configurar comportamiento de mocks
3. **Limpiar singletons en `tearDown()`** para evitar contaminación entre tests
4. **Invocar los métodos del controlador** - los tests deben llamar al método que están probando, no solo crear el controlador

Para más detalles sobre errores comunes y soluciones, consulta: `docs/issues/phpunit-mocking-request-response.md`
