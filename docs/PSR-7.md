# PSR-7 HTTP Message Interfaces

**Autor**: Pablo Bozzolo (boctulus)
**Fecha**: 2025-01-29
**Versión**: 1.0

---

## Introducción

SimpleRest ahora incluye **adaptadores PSR-7** que permiten interoperabilidad con librerías y frameworks que implementan el estándar [PSR-7: HTTP Message Interfaces](https://www.php-fig.org/psr/psr-7/).

Los adaptadores **envuelven** las clases existentes `Request` y `Response` sin modificarlas, manteniendo **100% de backward compatibility** con tu código actual.

---

## ¿Qué es PSR-7?

PSR-7 es un estándar de PHP-FIG que define interfaces comunes para representar mensajes HTTP:

- **RequestInterface** - Cliente HTTP request
- **ServerRequestInterface** - Server-side HTTP request
- **ResponseInterface** - HTTP response
- **MessageInterface** - Base para requests y responses
- **StreamInterface** - Message body streams
- **UriInterface** - URIs

### Beneficios de PSR-7

✅ **Inmutabilidad** - Métodos `with*()` retornan nuevas instancias
✅ **Interoperabilidad** - Funciona con librerías PSR-7 (Guzzle, Slim, etc.)
✅ **Estandarización** - API consistente entre frameworks
✅ **Type Safety** - Interfaces bien definidas

---

## Arquitectura

### Estructura de Archivos

```
app/Core/Psr7/
├── StreamAdapter.php          # PSR-7 StreamInterface
├── UriAdapter.php              # PSR-7 UriInterface
├── ServerRequestAdapter.php   # PSR-7 ServerRequestInterface
└── ResponseAdapter.php         # PSR-7 ResponseInterface

app/Core/Helpers/
└── psr7.php                    # Helper functions
```

### Patrón Adapter

Los adaptadores **NO modifican** Request/Response originales, sino que los **wrappean**:

```php
// Request original (tu código actual)
$request = Request::getInstance();
$data = $request->getBody();

// Adaptador PSR-7 (nuevo, optional)
$psr7Request = psr7_request();
$data = $psr7Request->getParsedBody();
```

---

## Uso Básico

### Helper Functions

```php
<?php

// Get PSR-7 ServerRequest from current Request
$request = psr7_request();

// Get PSR-7 Response
$response = psr7_response();

// Create Stream from string/array
$stream = psr7_stream('Hello, World!');

// Create URI
$uri = psr7_uri('https://example.com/path?query=value');

// Create JSON response
$jsonResponse = psr7_json(['success' => true], 200);

// Create redirect response
$redirect = psr7_redirect('https://example.com', 302);

// Create HTML response
$html = psr7_html('<h1>Title</h1>', 200);

// Create plain text response
$text = psr7_text('Plain text content', 200);
```

---

## Ejemplos de Uso

### 1. Crear una Respuesta JSON

```php
<?php

use function psr7_json;

// Forma PSR-7
$response = psr7_json([
    'message' => 'Success',
    'data' => $data
], 200);

// Agregar headers
$response = $response->withHeader('X-Custom-Header', 'value');

// Enviar (usa el Response de SimpleRest internamente)
$response->send();
```

### 2. Trabajar con Request PSR-7

```php
<?php

$request = psr7_request();

// Get query params
$queryParams = $request->getQueryParams();

// Get parsed body (JSON/form data)
$body = $request->getParsedBody();

// Get headers
$contentType = $request->getHeaderLine('Content-Type');

// Get URI
$uri = $request->getUri();
$path = $uri->getPath();
$query = $uri->getQuery();

// Inmutabilidad - crear nueva instancia modificada
$newRequest = $request->withQueryParams(['new' => 'value']);
```

### 3. Usar Atributos (PSR-7 Request Attributes)

Los atributos son útiles para pasar datos entre middlewares:

```php
<?php

$request = psr7_request();

// Agregar atributo
$request = $request->withAttribute('user_id', 123);

// Obtener atributo
$userId = $request->getAttribute('user_id');

// Obtener con default
$role = $request->getAttribute('role', 'guest');

// Remover atributo
$request = $request->withoutAttribute('user_id');
```

### 4. Trabajar con Streams

```php
<?php

use Boctulus\Simplerest\Core\Psr7\StreamAdapter;

// Crear stream desde string
$stream = psr7_stream('File content here');

// Leer contenido
$content = (string) $stream;

// Escribir
$stream->write('More content');

// Seek y read
$stream->rewind();
$chunk = $stream->read(1024);

// Metadata
$size = $stream->getSize();
$isReadable = $stream->isReadable();
$isWritable = $stream->isWritable();
```

### 5. Inmutabilidad en Responses

```php
<?php

$response = psr7_response();

// Cada with* retorna una NUEVA instancia
$response1 = $response->withStatus(404);
$response2 = $response1->withHeader('X-Custom', 'value');
$response3 = $response2->withJson(['error' => 'Not found'], 404);

// Fluent interface (chainable)
$finalResponse = psr7_response()
    ->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->withHeader('X-API-Version', '1.0')
    ->withJson(['success' => true]);
```

---

## Interoperabilidad con Librerías PSR-7

### Ejemplo: Usar con Guzzle HTTP Client

```php
<?php

use GuzzleHttp\Client;

$client = new Client();

// Tu Request de SimpleRest
$simpleRestRequest = request();

// Convertir a PSR-7 para usar con Guzzle
$psr7Request = psr7_request();

// Usar datos del request para hacer una API call
$response = $client->request(
    $psr7Request->getMethod(),
    'https://api.example.com/endpoint',
    [
        'headers' => $psr7Request->getHeaders(),
        'json' => $psr7Request->getParsedBody()
    ]
);
```

### Ejemplo: Middleware PSR-15 Compatible

```php
<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Validar token
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return psr7_json(['error' => 'Unauthorized'], 401);
        }

        // Agregar user_id como atributo
        $request = $request->withAttribute('user_id', 123);

        // Pasar al siguiente middleware
        return $handler->handle($request);
    }
}
```

---

## Migración Gradual

### Estrategia Recomendada

#### Fase 1: **No Cambiar Código Existente**
- ✅ Mantener `request()` y `response()` en código actual
- ✅ Usar `psr7_*()` solo en **nuevo código**
- ✅ Usar adaptadores para **interoperabilidad** con librerías PSR-7

#### Fase 2: **Adopción Gradual** (Opcional)
- ✅ Usar `psr7_request()` en nuevos controladores
- ✅ Aprovechar inmutabilidad en lógica compleja
- ✅ Usar atributos para pasar datos entre middlewares

#### Fase 3: **Modernización Completa** (Largo Plazo)
- ✅ Refactorizar código legacy a PSR-7
- ✅ Implementar middlewares PSR-15
- ✅ Deprecar métodos mutables

---

## Comparación: SimpleRest vs PSR-7

| Característica | SimpleRest (Actual) | PSR-7 Adapters |
|----------------|---------------------|----------------|
| **Mutabilidad** | ✅ Mutable (`shiftQuery()`) | ✅ Immutable (`withQueryParams()`) |
| **Singleton** | ✅ Usa singleton | ⚠️ Wrappea singleton |
| **Type Safety** | ⚠️ Mixed types | ✅ Strict interfaces |
| **Interoperabilidad** | ❌ No compatible con PSR-7 | ✅ Compatible con librerías PSR-7 |
| **Streams** | ❌ String/array body | ✅ StreamInterface |
| **Attributes** | ❌ No soporta | ✅ Request attributes |
| **Backward Compat** | ✅ N/A | ✅ 100% compatible |

---

## Testing con PSR-7

### Ejemplo de Unit Test

```php
<?php

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Psr7\ServerRequestAdapter;
use Boctulus\Simplerest\Core\Request;

class MyControllerTest extends TestCase
{
    public function testControllerWithPsr7()
    {
        // Create mock Request
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->any())
            ->method('getQuery')
            ->willReturn(['id' => '123']);

        // Wrap in PSR-7 adapter
        $psr7Request = new ServerRequestAdapter($mockRequest);

        // Pass to controller
        $controller = new MyController();
        $response = $controller->handle($psr7Request);

        // Assert PSR-7 response
        $this->assertEquals(200, $response->getStatusCode());
    }
}
```

---

## Preguntas Frecuentes

### ¿Debo migrar todo mi código a PSR-7?

**No**. Los adaptadores están diseñados para:
1. **Interoperabilidad** con librerías PSR-7
2. **Nuevo código** que quiera aprovechar inmutabilidad
3. **Migración gradual** opcional

Tu código actual sigue funcionando sin cambios.

### ¿Los adaptadores afectan el performance?

El overhead es **mínimo** porque:
- Los adaptadores son wrappers delgados
- Solo se crean cuando los necesitas
- El singleton de Request/Response se reutiliza

### ¿Puedo mezclar PSR-7 y código legacy?

**Sí**. Puedes usar ambos en el mismo proyecto:

```php
// Legacy
$data = request()->getBody();

// PSR-7
$psr7Data = psr7_request()->getParsedBody();
```

### ¿Cómo envío una Response PSR-7?

```php
$response = psr7_json(['success' => true]);
$response->send(); // Usa Response de SimpleRest internamente
```

---

## Referencias

- [PSR-7: HTTP Message Interfaces](https://www.php-fig.org/psr/psr-7/)
- [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- [PSR-17: HTTP Factories](https://www.php-fig.org/psr/psr-17/)
- [psr/http-message en Packagist](https://packagist.org/packages/psr/http-message)

---

## Soporte

¿Encontraste un bug o tienes una sugerencia?

- 📧 Email: boctulus@gmail.com
- 🐛 Issues: `docs/issues/`

---

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
