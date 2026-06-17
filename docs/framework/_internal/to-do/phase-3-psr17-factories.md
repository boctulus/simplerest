# Fase 3: PSR-17 HTTP Factories

**Autor**: Pablo Bozzolo (boctulus)
**Estado**: 📋 Planeado (No iniciado)
**Prioridad**: Media
**Dependencias**: Fase 1 (PSR-7 Adapters) ✅ Completada

---

## Objetivos

Implementar **PSR-17 HTTP Factories** para permitir la creación de objetos HTTP siguiendo el estándar PSR-17, proporcionando una forma consistente y estandarizada de crear requests, responses, streams y URIs.

---

## Contexto

### ¿Qué es PSR-17?

PSR-17 define interfaces para **factories** que crean objetos PSR-7:
- `RequestFactoryInterface` - Crea requests
- `ResponseFactoryInterface` - Crea responses
- `ServerRequestFactoryInterface` - Crea server requests
- `StreamFactoryInterface` - Crea streams
- `UriFactoryInterface` - Crea URIs
- `UploadedFileFactoryInterface` - Crea uploaded files

### ¿Por qué implementar PSR-17?

1. **Interoperabilidad**: Librerías PSR-17 pueden crear objetos PSR-7 de SimpleRest
2. **Dependency Injection**: Inyectar factories en lugar de instancias concretas
3. **Testing**: Facilita crear mocks y fixtures
4. **Estandarización**: Sigue patrones reconocidos por la comunidad PHP

---

## Alcance de Implementación

### Interfaces a Implementar

#### 1. `ResponseFactoryInterface`

```php
namespace Boctulus\Simplerest\Core\Psr17;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Create a new response.
     *
     * @param int $code HTTP status code; defaults to 200
     * @param string $reasonPhrase Reason phrase to associate with status code
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        // TODO: Implementar
    }
}
```

**Uso esperado:**
```php
$factory = new ResponseFactory();
$response = $factory->createResponse(201, 'Created');
$response = $response->withJson(['id' => 123]);
```

#### 2. `ServerRequestFactoryInterface`

```php
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        // TODO: Implementar
    }
}
```

**Uso esperado:**
```php
$factory = new ServerRequestFactory();
$request = $factory->createServerRequest('POST', '/api/users', $_SERVER);
```

#### 3. `StreamFactoryInterface`

```php
class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        // TODO: Usar StreamAdapter existente
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        // TODO: Implementar
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        // TODO: Implementar
    }
}
```

**Uso esperado:**
```php
$factory = new StreamFactory();
$stream = $factory->createStream('Hello World');
$fileStream = $factory->createStreamFromFile('/path/to/file.json');
```

#### 4. `UriFactoryInterface`

```php
class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        // TODO: Usar UriAdapter existente
    }
}
```

**Uso esperado:**
```php
$factory = new UriFactory();
$uri = $factory->createUri('https://example.com/api/users?page=1');
```

#### 5. `RequestFactoryInterface`

```php
class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        // TODO: Implementar
    }
}
```

#### 6. `UploadedFileFactoryInterface` (Opcional - Prioridad Baja)

```php
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        // TODO: Implementar
    }
}
```

---

## Tareas de Implementación

### Fase 3.1: Factories Básicas (CORE)

- [ ] Instalar dependencia `psr/http-factory:^1.0` via Composer
- [ ] Crear directorio `app/Core/Psr17/`
- [ ] Implementar `ResponseFactory.php`
- [ ] Implementar `ServerRequestFactory.php`
- [ ] Implementar `StreamFactory.php`
- [ ] Implementar `UriFactory.php`
- [ ] Implementar `RequestFactory.php`

### Fase 3.2: Helpers

- [ ] Crear `app/Core/Helpers/psr17.php` con funciones de conveniencia:
  ```php
  function psr17_response_factory(): ResponseFactoryInterface
  function psr17_request_factory(): RequestFactoryInterface
  function psr17_stream_factory(): StreamFactoryInterface
  function psr17_uri_factory(): UriFactoryInterface
  ```

### Fase 3.3: Integración con Factories Existentes

- [ ] Actualizar `app/Core/Helpers/factories.php` para soportar PSR-17
- [ ] Crear alias/wrappers si es necesario para mantener backward compatibility

### Fase 3.4: Testing

- [ ] Crear `tests/unit-tests/Psr17FactoriesTest.php`
- [ ] Test para `ResponseFactory::createResponse()`
- [ ] Test para `ServerRequestFactory::createServerRequest()`
- [ ] Test para `StreamFactory::createStream()`
- [ ] Test para `StreamFactory::createStreamFromFile()`
- [ ] Test para `UriFactory::createUri()`
- [ ] Test para `RequestFactory::createRequest()`
- [ ] **Meta**: 15+ tests, 50+ assertions, 100% passing

### Fase 3.5: Documentación

- [ ] Crear `docs/PSR-17.md` con:
  - Introducción a PSR-17
  - Uso de cada factory
  - Ejemplos de integración con librerías externas
  - Comparación con helpers existentes
  - Casos de uso (DI, testing, etc.)
  - FAQs
- [ ] Actualizar `docs/CHANGELOG-PSR.md` con cambios de Fase 3
- [ ] Actualizar `README.md` con mención a PSR-17

---

## Estimación de Esfuerzo

| Tarea | Tiempo Estimado | Complejidad |
|-------|-----------------|-------------|
| Implementar factories básicas | 3-4 horas | Media |
| Helpers y wrappers | 1 hora | Baja |
| Testing completo | 2-3 horas | Media |
| Documentación | 2 horas | Baja |
| **TOTAL** | **8-10 horas** | **Media** |

---

## Dependencias Externas

### Composer

```bash
composer require psr/http-factory:^1.0
```

**Paquetes relacionados:**
- `psr/http-message:^2.0` (ya instalado en Fase 1)
- `psr/http-factory:^1.0` (nuevo)

---

## Riesgos y Mitigaciones

### Riesgo 1: Conflicto con Factories Existentes

**Descripción**: SimpleRest ya tiene `app/Core/Helpers/factories.php` con funciones `request()` y `response()`.

**Mitigación**:
- Usar namespace `Psr17` para nuevas factories
- Mantener helpers existentes sin cambios
- Crear nuevos helpers con prefijo `psr17_*`

### Riesgo 2: Overhead de Objetos

**Descripción**: Crear objetos vía factories puede tener overhead.

**Mitigación**:
- Factories son opt-in, no obligatorias
- Código existente sigue usando helpers directos
- Solo usar factories cuando se necesite interoperabilidad PSR

### Riesgo 3: Complejidad para Usuarios

**Descripción**: Usuarios pueden confundirse entre helpers y factories.

**Mitigación**:
- Documentación clara sobre cuándo usar cada uno
- Ejemplos de casos de uso específicos
- Guía de migración gradual

---

## Casos de Uso

### 1. Dependency Injection

```php
class UserController
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory
    ) {}

    public function show(int $id): ResponseInterface
    {
        $user = $this->userRepository->find($id);

        return $this->responseFactory
            ->createResponse(200)
            ->withJson(['user' => $user]);
    }
}
```

### 2. Testing con Mocks

```php
public function testControllerReturnsJson()
{
    $mockFactory = $this->createMock(ResponseFactoryInterface::class);
    $mockFactory->method('createResponse')
        ->willReturn(new ResponseAdapter(Response::getInstance()));

    $controller = new UserController($mockFactory);
    $response = $controller->show(123);

    $this->assertInstanceOf(ResponseInterface::class, $response);
}
```

### 3. Integración con Librerías PSR-17

```php
use Psr\Http\Message\ResponseFactoryInterface;

// Librería externa espera PSR-17 factory
$middleware = new ExternalMiddleware(
    new ResponseFactory() // Nuestra implementación PSR-17
);
```

---

## Criterios de Aceptación

- ✅ Todas las factories implementan interfaces PSR-17
- ✅ 100% de tests pasan (15+ tests, 50+ assertions)
- ✅ Documentación completa en `docs/PSR-17.md`
- ✅ Helpers de conveniencia creados
- ✅ 100% backward compatible (no breaking changes)
- ✅ Ejemplos de uso en documentación
- ✅ Integración testeada con al menos 1 librería PSR-17 externa

---

## Referencias

- [PSR-17: HTTP Factories](https://www.php-fig.org/psr/psr-17/)
- [PSR-7: HTTP Message Interfaces](https://www.php-fig.org/psr/psr-7/)
- [Packagist: psr/http-factory](https://packagist.org/packages/psr/http-factory)

---

## Notas Adicionales

### Orden Recomendado de Implementación

1. **Primero**: `StreamFactory` y `UriFactory` (más simples, reutilizan adapters existentes)
2. **Segundo**: `ResponseFactory` (reutiliza ResponseAdapter)
3. **Tercero**: `ServerRequestFactory` (reutiliza ServerRequestAdapter)
4. **Cuarto**: `RequestFactory` (nuevo, requiere RequestAdapter)
5. **Último**: `UploadedFileFactory` (opcional, puede postergarse)

### Archivos a Crear

```
app/Core/Psr17/
├── ResponseFactory.php
├── ServerRequestFactory.php
├── StreamFactory.php
├── UriFactory.php
├── RequestFactory.php
└── UploadedFileFactory.php (opcional)

app/Core/Helpers/
└── psr17.php

tests/unit-tests/
└── Psr17FactoriesTest.php

docs/
├── PSR-17.md
└── CHANGELOG-PSR.md (actualizar)
```

---

**Última Actualización**: 2025-01-29
**Estado**: 📋 Planeado
**Siguiente Fase**: [Fase 4: PSR-15 Middleware](./phase-4-psr15-middleware.md)
