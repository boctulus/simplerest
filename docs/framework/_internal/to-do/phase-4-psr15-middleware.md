# Fase 4: PSR-15 HTTP Server Request Handlers

**Autor**: Pablo Bozzolo (boctulus)
**Estado**: 📋 Planeado (No iniciado)
**Prioridad**: Alta
**Dependencias**:
- Fase 1 (PSR-7 Adapters) ✅ Completada
- Fase 3 (PSR-17 Factories) ⏳ Planeada

---

## Objetivos

Implementar **PSR-15 HTTP Server Request Handlers** para permitir el uso de middlewares estandarizados siguiendo el patrón PSR-15, proporcionando un pipeline de procesamiento de requests compatible con el ecosistema PHP moderno.

---

## Contexto

### ¿Qué es PSR-15?

PSR-15 define dos interfaces clave para el manejo de requests HTTP:

1. **`MiddlewareInterface`**: Define un middleware que procesa un request y delega al siguiente handler
2. **`RequestHandlerInterface`**: Define un handler que procesa un request y retorna un response

### Arquitectura PSR-15

```
Request → Middleware1 → Middleware2 → Middleware3 → Handler → Response
            ↓              ↓              ↓            ↓
         (Auth)      (Validation)    (Logging)   (Controller)
```

### ¿Por qué implementar PSR-15?

1. **Middlewares Reutilizables**: Usar middlewares de terceros (CORS, Auth, etc.)
2. **Pipeline Estandarizado**: Patrón reconocido en frameworks modernos (Slim, Mezzio)
3. **Separation of Concerns**: Cada middleware tiene una responsabilidad única
4. **Composición**: Encadenar middlewares de forma declarativa
5. **Testing**: Fácil testear middlewares en aislamiento

---

## Estado Actual de SimpleRest

### Middlewares Existentes

SimpleRest **YA tiene middlewares**, pero **NO son PSR-15**:

**Ubicación**: `app/Middlewares/`

**Ejemplos existentes**:
- `InyectarInfoEmpresa.php`
- `InyectarSaludo.php`
- `InyectarUsername.php`

**Patrón actual**:
```php
class InyectarUsername
{
    function handle()
    {
        $req = request();
        $req->username = auth()->user()['username'] ?? null;
    }
}
```

### Problemas del Patrón Actual

1. ❌ **No es PSR-15**: No implementa `MiddlewareInterface`
2. ❌ **Muta estado global**: Modifica `request()` directamente
3. ❌ **No retorna response**: No sigue patrón de pipeline
4. ❌ **No es composable**: No puede encadenarse con middlewares PSR-15
5. ❌ **Dificulta testing**: Depende de singletons globales

---

## Alcance de Implementación

### Fase 4.1: Interfaces PSR-15

#### 1. `MiddlewareInterface`

```php
namespace Psr\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
```

#### 2. `RequestHandlerInterface`

```php
namespace Psr\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
```

### Fase 4.2: Middleware Dispatcher/Pipeline

Implementar un **dispatcher** que ejecute middlewares en orden:

```php
namespace Boctulus\Simplerest\Core\Psr15;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareDispatcher implements RequestHandlerInterface
{
    private array $middlewares = [];
    private RequestHandlerInterface $defaultHandler;

    public function __construct(RequestHandlerInterface $defaultHandler)
    {
        $this->defaultHandler = $defaultHandler;
    }

    public function add(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // TODO: Implementar pipeline recursivo
        // 1. Si no hay más middlewares, ejecutar defaultHandler
        // 2. Si hay middlewares, ejecutar el próximo y pasar handler recursivo
    }
}
```

### Fase 4.3: Adaptar Middlewares Existentes

Crear **adapters** para middlewares existentes:

```php
namespace Boctulus\Simplerest\Core\Psr15\Adapters;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class LegacyMiddlewareAdapter implements MiddlewareInterface
{
    private $legacyMiddleware;

    public function __construct($legacyMiddleware)
    {
        $this->legacyMiddleware = $legacyMiddleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Ejecutar middleware viejo (muta request global)
        $this->legacyMiddleware->handle();

        // 2. Continuar pipeline
        return $handler->handle($request);
    }
}
```

### Fase 4.4: Middlewares PSR-15 Nuevos

Crear middlewares **PSR-15 nativos** como ejemplos:

#### Ejemplo: AuthMiddleware PSR-15

```php
namespace Boctulus\Simplerest\Middlewares\Psr15;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            // Retornar 401 sin continuar pipeline
            return psr7_json(['error' => 'Unauthorized'], 401);
        }

        $userId = $this->validateToken($token);

        // Agregar user_id al request usando withAttribute (inmutable)
        $request = $request->withAttribute('user_id', $userId);

        // Continuar pipeline con request modificado
        return $handler->handle($request);
    }

    private function validateToken(string $token): int
    {
        // TODO: Validar token JWT
    }
}
```

#### Ejemplo: CorsMiddleware PSR-15

```php
class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Manejar preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return psr7_response()
                ->withStatus(204)
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        // Procesar request normal
        $response = $handler->handle($request);

        // Agregar headers CORS a response
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*');
    }
}
```

#### Ejemplo: LoggingMiddleware PSR-15

```php
class LoggingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $start = microtime(true);

        // Ejecutar siguiente middleware/handler
        $response = $handler->handle($request);

        $duration = microtime(true) - $start;

        // Logging inmutable (no afecta response)
        Logger::log([
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'status' => $response->getStatusCode(),
            'duration_ms' => round($duration * 1000, 2)
        ]);

        return $response;
    }
}
```

---

## Tareas de Implementación

### Fase 4.1: Instalación PSR-15

- [ ] Instalar `psr/http-server-handler:^1.0` via Composer
- [ ] Instalar `psr/http-server-middleware:^1.0` via Composer

### Fase 4.2: Middleware Dispatcher

- [ ] Crear directorio `app/Core/Psr15/`
- [ ] Implementar `MiddlewareDispatcher.php` (pipeline handler)
- [ ] Implementar `CallableRequestHandler.php` (wrappea closures como handlers)
- [ ] Implementar método `handle()` con recursión para pipeline

### Fase 4.3: Adapters para Middlewares Viejos

- [ ] Crear `app/Core/Psr15/Adapters/LegacyMiddlewareAdapter.php`
- [ ] Adaptar middlewares existentes en `app/Middlewares/`:
  - [ ] `InyectarInfoEmpresa`
  - [ ] `InyectarSaludo`
  - [ ] `InyectarUsername`

### Fase 4.4: Middlewares PSR-15 Nuevos

- [ ] Crear directorio `app/Middlewares/Psr15/`
- [ ] Implementar `AuthMiddleware.php` (ejemplo de autenticación)
- [ ] Implementar `CorsMiddleware.php` (ejemplo de CORS)
- [ ] Implementar `LoggingMiddleware.php` (ejemplo de logging)
- [ ] Implementar `ValidationMiddleware.php` (ejemplo de validación)

### Fase 4.5: Integración con WebRouter

- [ ] Actualizar `app/Core/WebRouter.php` para soportar PSR-15 middlewares
- [ ] Agregar método `addMiddleware(MiddlewareInterface $middleware)`
- [ ] Ejecutar pipeline antes de controller
- [ ] Mantener backward compatibility con middlewares viejos

### Fase 4.6: Testing

- [ ] Crear `tests/unit-tests/Psr15MiddlewareTest.php`
- [ ] Test para `MiddlewareDispatcher` con 1 middleware
- [ ] Test para `MiddlewareDispatcher` con múltiples middlewares (orden)
- [ ] Test para `AuthMiddleware` (401 sin token, 200 con token)
- [ ] Test para `CorsMiddleware` (OPTIONS preflight)
- [ ] Test para `LoggingMiddleware` (no afecta response)
- [ ] Test para `LegacyMiddlewareAdapter`
- [ ] **Meta**: 20+ tests, 60+ assertions, 100% passing

### Fase 4.7: Documentación

- [ ] Crear `docs/PSR-15.md` con:
  - Introducción a PSR-15
  - Arquitectura del pipeline
  - Cómo crear middlewares PSR-15
  - Ejemplos de uso
  - Comparación con middlewares viejos
  - Migración de middlewares legacy a PSR-15
  - Best practices
  - FAQs
- [ ] Crear `docs/Middlewares.md` (guía general de middlewares)
- [ ] Actualizar `docs/CHANGELOG-PSR.md` con Fase 4

---

## Estimación de Esfuerzo

| Tarea | Tiempo Estimado | Complejidad |
|-------|-----------------|-------------|
| Middleware Dispatcher | 3-4 horas | Alta |
| Adapters para middlewares viejos | 2 horas | Media |
| Middlewares PSR-15 nuevos (4x) | 3-4 horas | Media |
| Integración con WebRouter | 2-3 horas | Alta |
| Testing completo | 4-5 horas | Alta |
| Documentación | 3 horas | Media |
| **TOTAL** | **17-21 horas** | **Alta** |

---

## Dependencias Externas

### Composer

```bash
composer require psr/http-server-handler:^1.0
composer require psr/http-server-middleware:^1.0
```

**Paquetes relacionados:**
- `psr/http-message:^2.0` (ya instalado en Fase 1)
- `psr/http-factory:^1.0` (instalado en Fase 3)
- `psr/http-server-handler:^1.0` (nuevo)
- `psr/http-server-middleware:^1.0` (nuevo)

---

## Riesgos y Mitigaciones

### Riesgo 1: Breaking Changes en WebRouter

**Descripción**: Integrar PSR-15 puede romper routing existente.

**Mitigación**:
- Mantener middlewares viejos funcionando con `LegacyMiddlewareAdapter`
- Nuevos middlewares PSR-15 son opt-in
- Tests exhaustivos antes de merge

### Riesgo 2: Performance Overhead

**Descripción**: Pipeline con múltiples middlewares puede agregar latencia.

**Mitigación**:
- Benchmarking antes/después
- Middlewares solo se ejecutan cuando se configuran
- Lazy loading de middlewares pesados

### Riesgo 3: Complejidad de Migración

**Descripción**: Usuarios tienen que reescribir middlewares.

**Mitigación**:
- `LegacyMiddlewareAdapter` permite usar middlewares viejos sin cambios
- Documentación clara con ejemplos de migración
- Guía paso a paso

### Riesgo 4: Orden de Ejecución

**Descripción**: Orden de middlewares es crítico (ej: Auth antes de CORS).

**Mitigación**:
- Documentar orden recomendado
- Ejemplos con pipelines completos
- Tests de integración con múltiples middlewares

---

## Casos de Uso

### 1. Pipeline de API REST

```php
use Boctulus\Simplerest\Core\Psr15\MiddlewareDispatcher;
use Boctulus\Simplerest\Middlewares\Psr15\{CorsMiddleware, AuthMiddleware, LoggingMiddleware};

// Crear handler final (controller)
$handler = new CallableRequestHandler(function($request) {
    return psr7_json(['data' => 'success']);
});

// Crear pipeline
$dispatcher = new MiddlewareDispatcher($handler);
$dispatcher
    ->add(new LoggingMiddleware())      // 1. Logging primero
    ->add(new CorsMiddleware())         // 2. CORS segundo
    ->add(new AuthMiddleware());        // 3. Auth último

// Ejecutar
$response = $dispatcher->handle(psr7_request());
```

### 2. Middleware Condicional

```php
class RateLimitMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'];

        if ($this->isRateLimited($ip)) {
            return psr7_json(['error' => 'Too many requests'], 429);
        }

        return $handler->handle($request);
    }
}
```

### 3. Middleware que Modifica Request

```php
class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $body = json_decode((string) $request->getBody(), true);
            $request = $request->withParsedBody($body);
        }

        return $handler->handle($request);
    }
}
```

### 4. Middleware que Modifica Response

```php
class CompressionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $acceptEncoding = $request->getHeaderLine('Accept-Encoding');

        if (str_contains($acceptEncoding, 'gzip')) {
            $body = gzencode((string) $response->getBody());
            $stream = psr7_stream($body);

            return $response
                ->withBody($stream)
                ->withHeader('Content-Encoding', 'gzip');
        }

        return $response;
    }
}
```

---

## Integración con WebRouter

### Configuración en routes.php

```php
use Boctulus\Simplerest\Middlewares\Psr15\{AuthMiddleware, CorsMiddleware};

// Middlewares globales (se ejecutan en todas las rutas)
WebRouter::addGlobalMiddleware(new CorsMiddleware());
WebRouter::addGlobalMiddleware(new LoggingMiddleware());

// Middlewares por ruta
WebRouter::group(['prefix' => '/api', 'middleware' => [new AuthMiddleware()]], function() {
    WebRouter::get('/users', [UserController::class, 'index']);
    WebRouter::post('/users', [UserController::class, 'store']);
});

// Middlewares específicos
WebRouter::get('/public', [PublicController::class, 'index'])
    ->middleware([new CorsMiddleware()]);
```

---

## Criterios de Aceptación

- ✅ `MiddlewareDispatcher` ejecuta middlewares en orden correcto
- ✅ Middlewares PSR-15 pueden retornar response tempranamente (short-circuit)
- ✅ Middlewares pueden modificar request y response (inmutablemente)
- ✅ `LegacyMiddlewareAdapter` permite usar middlewares viejos
- ✅ 100% de tests pasan (20+ tests, 60+ assertions)
- ✅ Documentación completa en `docs/PSR-15.md`
- ✅ Integración con WebRouter no rompe rutas existentes
- ✅ Al menos 4 middlewares PSR-15 de ejemplo creados
- ✅ 100% backward compatible (middlewares viejos siguen funcionando)

---

## Middlewares PSR-15 a Crear

### Prioritarios (Fase 4)

1. ✅ `AuthMiddleware` - Autenticación JWT/Bearer
2. ✅ `CorsMiddleware` - CORS headers
3. ✅ `LoggingMiddleware` - Request/response logging
4. ✅ `ValidationMiddleware` - Validación de input

### Opcionales (Post-Fase 4)

5. `RateLimitMiddleware` - Rate limiting por IP
6. `CompressionMiddleware` - Gzip compression
7. `JsonBodyParserMiddleware` - Parse JSON body
8. `CsrfMiddleware` - CSRF protection
9. `CacheMiddleware` - HTTP caching headers
10. `ContentNegotiationMiddleware` - Content negotiation (Accept header)

---

## Referencias

- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- [PSR-7: HTTP Message Interfaces](https://www.php-fig.org/psr/psr-7/)
- [Middleware Pattern Explained](https://refactoring.guru/design-patterns/chain-of-responsibility)
- [Slim Framework Middleware](https://www.slimframework.com/docs/v4/concepts/middleware.html)

---

## Notas Adicionales

### Orden Recomendado de Middlewares

```
Request
  ↓
1. LoggingMiddleware (logging primero para capturar todo)
  ↓
2. CorsMiddleware (CORS antes de auth para permitir preflight)
  ↓
3. AuthMiddleware (autenticación)
  ↓
4. ValidationMiddleware (validación de input)
  ↓
5. Controller/Handler (lógica de negocio)
  ↓
Response
```

### Archivos a Crear

```
app/Core/Psr15/
├── MiddlewareDispatcher.php
├── CallableRequestHandler.php
└── Adapters/
    └── LegacyMiddlewareAdapter.php

app/Middlewares/Psr15/
├── AuthMiddleware.php
├── CorsMiddleware.php
├── LoggingMiddleware.php
└── ValidationMiddleware.php

tests/unit-tests/
└── Psr15MiddlewareTest.php

docs/
├── PSR-15.md
├── Middlewares.md
└── CHANGELOG-PSR.md (actualizar)
```

---

**Última Actualización**: 2025-01-29
**Estado**: 📋 Planeado
**Fase Anterior**: [Fase 3: PSR-17 Factories](./phase-3-psr17-factories.md)
**Siguiente Fase**: [Fase 5: StreamInterface Body](./phase-5-stream-interface.md)
