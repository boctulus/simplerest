# Issue: Errores en Mocking de Request y Response en PHPUnit

**Fecha**: 2025-11-27
**Categoría**: Testing / Unit Tests
**Severidad**: Alta
**Estado**: ✅ Resuelto

---

## Descripción del Problema

Al ejecutar pruebas unitarias con PHPUnit en el framework SimpleRest, se presentaban múltiples errores relacionados con el mocking de las clases `Request` y `Response`:

### Errores Comunes

#### 1. Error: "Call to a member function with() on null"

```
Error: Call to a member function with() on null
D:\laragon\www\simplerest\packages\boctulus\friendlypos-web\tests\OpenFacturaControllerErrorTest.php:103
```

**Causa**: Configuración incorrecta de mocks en PHPUnit. El método `method()` no retorna un objeto válido para encadenar `with()`.

#### 2. Error: "Cannot use object of type stdClass as array"

```
Error: Cannot use object of type stdClass as array
D:\laragon\www\simplerest\packages\boctulus\friendlypos-web\src\Controllers\OpenFacturaController.php:62
```

**Causa**: El mock retorna un objeto `stdClass` en lugar de un array cuando se esperaba que `getBody(true)` devolviera un array.

#### 3. Failure: "Method was expected to be called 1 time, actually called 0 times"

```
Expectation failed for method name is "status" when invoked 1 time.
Method was expected to be called 1 time, actually called 0 times.
```

**Causa**: Los mocks no están siendo utilizados por el código bajo prueba porque no fueron inyectados correctamente en los singletons del framework.

---

## Análisis de la Causa Raíz

El framework SimpleRest utiliza el **patrón Singleton** para gestionar las instancias de `Request` y `Response`:

```php
// En app/Core/Libs/Factory.php
static function request() : Request {
    return Request::getInstance();
}

static function response($data = null, ?int $http_code = 200) : Response {
    return Response::getInstance();
}
```

Los controladores acceden a estas instancias mediante helpers globales:

```php
// En el controlador
$data = request()->getBody(true);
response()->status(200);
response()->json(['success' => true]);
```

**El problema**: Los tests intentaban mockear estos objetos incorrectamente de dos formas:

1. **Sintaxis incorrecta de PHPUnit** para configurar el comportamiento del mock
2. **No inyectar los mocks** en los singletons del framework, usando `$GLOBALS` en su lugar

---

## Solución

### ✅ Paso 1: Configurar Mocks con Sintaxis Correcta

**❌ ANTES (Incorrecto):**

```php
$mockRequest = $this->createMock(Request::class);
$mockRequest->method('getBody')->with(true)->willReturn($requestBody);
```

**Problema**: `method()` retorna `null` cuando se intenta encadenar `with()` directamente.

**✅ DESPUÉS (Correcto):**

```php
$mockRequest = $this->createMock(Request::class);
$mockRequest->expects($this->any())
    ->method('getBody')
    ->with(true)
    ->willReturn($requestBody);
```

**Explicación**:
- `expects($this->any())` configura que el método puede ser llamado cualquier cantidad de veces
- La cadena `->method()->with()->willReturn()` ahora funciona correctamente
- Esto asegura que el mock retorne un **array** real en lugar de un `stdClass`

### ✅ Paso 2: Inyectar Mocks en Singletons

**❌ ANTES (Incorrecto):**

```php
$GLOBALS['mockRequest'] = $mockRequest;
$GLOBALS['mockResponse'] = $mockResponse;
```

**Problema**: El framework no usa `$GLOBALS` para acceder a Request/Response. Usa `getInstance()`.

**✅ DESPUÉS (Correcto):**

```php
\Boctulus\Simplerest\Core\Request::setInstance($mockRequest);
\Boctulus\Simplerest\Core\Response::setInstance($mockResponse);
```

**Explicación**:
- `setInstance()` inyecta el mock directamente en el singleton
- Cuando el controlador llama a `request()` o `response()`, obtiene el mock inyectado
- Esto hace que los métodos mockeados (`getBody()`, `status()`, `json()`) sean realmente invocados

### ✅ Paso 3: Limpiar Singletons entre Tests

Para evitar contaminación entre tests, siempre limpiar los singletons en `tearDown()`:

```php
protected function tearDown(): void
{
    // Reset singleton instances to clean state
    \Boctulus\Simplerest\Core\Request::setInstance(null);
    \Boctulus\Simplerest\Core\Response::setInstance(null);

    parent::tearDown();
}
```

### ✅ Paso 4: Invocar los Métodos del Controlador

**Problema común**: Muchos tests creaban el controlador pero nunca invocaban el método a probar.

**❌ Test Incompleto:**

```php
public function testAnularGuiaDespachoMissingData()
{
    $mockRequest = $this->createMock(Request::class);
    // ... configurar mocks ...

    $controller = new OpenFacturaController();
    // ❌ Nunca se llama al método anularGuiaDespacho()
}
```

**✅ Test Completo:**

```php
public function testAnularGuiaDespachoMissingData()
{
    $mockRequest = $this->createMock(Request::class);
    // ... configurar mocks ...

    $controller = new OpenFacturaController();

    // ✅ Invocar el método usando Reflection
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('anularGuiaDespacho');
    $method->invoke($controller);
}
```

---

## Ejemplo Completo de Test Corregido

```php
<?php

use PHPUnit\Framework\TestCase;
use Boctulus\FriendlyposWeb\Controllers\OpenFacturaController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

class OpenFacturaControllerErrorTest extends TestCase
{
    private $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();

        // Store original environment values
        $this->originalEnv = [
            'OPENFACTURA_SANDBOX' => getenv('OPENFACTURA_SANDBOX'),
            'OPENFACTURA_API_KEY_DEV' => getenv('OPENFACTURA_API_KEY_DEV'),
        ];

        // Set test environment variables
        putenv('OPENFACTURA_SANDBOX=true');
        putenv('OPENFACTURA_API_KEY_DEV=test_api_key');
    }

    protected function tearDown(): void
    {
        // Restore original environment values
        foreach ($this->originalEnv as $key => $value) {
            if ($value !== false) {
                putenv("$key=$value");
            } else {
                putenv($key);
            }
        }

        // Reset singleton instances to clean state
        \Boctulus\Simplerest\Core\Request::setInstance(null);
        \Boctulus\Simplerest\Core\Response::setInstance(null);

        parent::tearDown();
    }

    public function testEmitDTEWithSdkException()
    {
        $requestBody = [
            'dteData' => [
                'Encabezado' => [
                    'IdDoc' => ['TipoDTE' => 33],
                ],
                'Emisor' => ['RUTEmisor' => '76399751-9'],
                'Receptor' => ['RUTRecep' => '76399751-9'],
                'Detalle' => [
                    [
                        'NmbItem' => 'Producto de prueba',
                        'QtyItem' => 1,
                        'PrcItem' => 1000,
                    ],
                ],
            ]
        ];

        // ✅ Create mock Request with correct syntax
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getBody')
            ->with(true)
            ->willReturn($requestBody);

        // ✅ Create mock Response
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

        // ✅ Inject mocks into singleton instances
        Request::setInstance($mockRequest);
        Response::setInstance($mockResponse);

        $controller = new OpenFacturaController();

        // ✅ Mock the SDK to throw an exception
        $reflection = new \ReflectionClass($controller);
        $sdkProperty = $reflection->getProperty('sdk');
        $sdkProperty->setAccessible(true);

        $mockSdk = $this->getMockBuilder(OpenFacturaSDKMock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSdk->method('emitirDTE')
            ->willThrowException(new Exception('SDK Error: Failed to emit DTE'));

        $sdkProperty->setValue($controller, $mockSdk);

        // ✅ Invoke the method being tested
        $reflection->getMethod('emitDTE')->invoke($controller);

        $this->assertTrue(true);
    }
}
```

---

## Resultados

### Antes de la Corrección

```
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

EEEEEEEE                                                            8 / 8 (100%)

Time: 00:00.042, Memory: 10.00 MB

There were 8 errors:

1) OpenFacturaControllerErrorTest::testEmitDTEWithSdkException
Error: Call to a member function with() on null
...

ERRORS!
Tests: 8, Assertions: 0, Errors: 8.
```

### Después de la Corrección

```
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

........                                                            8 / 8 (100%)

Time: 00:00.046, Memory: 12.00 MB

OK (8 tests, 16 assertions)
```

---

## Checklist de Corrección

Cuando encuentres errores similares en tests, verifica:

- [ ] ¿Los mocks usan `expects()->method()->with()->willReturn()`?
- [ ] ¿Los mocks se inyectan con `Request::setInstance()` y `Response::setInstance()`?
- [ ] ¿El `tearDown()` limpia los singletons con `setInstance(null)`?
- [ ] ¿El test invoca el método del controlador que se está probando?
- [ ] ¿Los mocks de `getBody()` retornan **arrays** y no objetos?
- [ ] ¿Las expectativas de mocks (`expects($this->once())`) son realistas?

---

## Archivos Corregidos

1. `packages/boctulus/friendlypos-web/tests/OpenFacturaControllerErrorTest.php` ✅
2. `packages/boctulus/friendlypos-web/tests/OpenFacturaControllerTest.php` ⚠️ (parcialmente)
3. `packages/boctulus/friendlypos-web/tests/OpenFacturaControllerSdkTest.php` ⚠️ (parcialmente)

**Nota**: Los archivos 2 y 3 aún tienen tests incompletos que no invocan los métodos del controlador.

---

## Referencias

- Documentación PHPUnit: https://phpunit.de/manual/10.5/en/test-doubles.html
- Patrón Singleton en PHP: https://refactoring.guru/design-patterns/singleton/php/example
- `docs/unit-tests-pruebas-unitarias.md`: Guía de unit testing del framework

---

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
