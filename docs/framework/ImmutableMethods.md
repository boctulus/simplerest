# Métodos Inmutables en Request y Response (Fase 2)

**Autor**: Pablo Bozzolo (boctulus)
**Fecha**: 2025-01-29
**Versión**: 1.0

---

## Introducción

A partir de la **Fase 2**, las clases `Request` y `Response` de SimpleRest incluyen **métodos inmutables** inspirados en PSR-7.

Estos métodos permiten trabajar con requests y responses de forma funcional, sin modificar las instancias originales.

---

## ¿Qué son los Métodos Inmutables?

Los métodos inmutables **NO modifican** la instancia original. En su lugar, **retornan una nueva instancia** con los cambios aplicados.

### Comparación: Mutable vs Inmutable

#### ❌ Método Mutable (Viejo Estilo)

```php
$request = Request::getInstance();
$request->shiftQuery('user_id'); // MODIFICA la instancia original
```

#### ✅ Método Inmutable (Nuevo Estilo - Recomendado)

```php
$request = Request::getInstance();
$modified = $request->withQueryParam('user_id', 123); // CREA nueva instancia

// $request NO fue modificado
// $modified es una NUEVA instancia con user_id=123
```

---

## Métodos Inmutables de Request

### 1. `withQueryParam(string $key, $value): Request`

Retorna una **nueva instancia** con el parámetro de query especificado.

```php
$request = Request::getInstance();
$modified = $request->withQueryParam('page', 2);

// Original permanece sin cambios
echo $request->get('page'); // null (si no existía)

// Nueva instancia tiene el parámetro
echo $modified->get('page'); // 2
```

### 2. `withoutQueryParam(string $key): Request`

Retorna una **nueva instancia** sin el parámetro de query especificado.

```php
$request = Request::getInstance();
$withoutPage = $request->withoutQueryParam('page');

// Si el request tenía page=5
echo $request->get('page');        // 5
echo $withoutPage->get('page');    // null
```

### 3. `withHeader(string $name, $value): Request`

Retorna una **nueva instancia** con el header especificado.

```php
$request = Request::getInstance();
$modified = $request->withHeader('X-Custom-Header', 'my-value');

echo $modified->getHeader('X-Custom-Header'); // "my-value"
```

**Nota**: Los headers son **case-insensitive**.

```php
$modified->getHeader('x-custom-header'); // "my-value" ✅
$modified->getHeader('X-CUSTOM-HEADER'); // "my-value" ✅
```

### 4. `withAddedHeader(string $name, $value): Request`

Retorna una **nueva instancia** con el header **agregado** (no reemplaza el existente).

```php
$request = Request::getInstance();
$modified = $request
    ->withHeader('X-Custom', 'value1')
    ->withAddedHeader('X-Custom', 'value2');

// Ambos valores están presentes
```

### 5. `withoutHeader(string $name): Request`

Retorna una **nueva instancia** sin el header especificado.

```php
$request = Request::getInstance();
$withoutAuth = $request->withoutHeader('Authorization');

echo $withoutAuth->getHeader('Authorization'); // null
```

### 6. `withBody($body): Request`

Retorna una **nueva instancia** con el body especificado.

```php
$request = Request::getInstance();
$modified = $request->withBody(['user_id' => 123, 'name' => 'John']);

$body = $modified->getBody(false); // ['user_id' => 123, 'name' => 'John']
```

---

## Métodos Inmutables de Response

### 1. `withStatus(int $code, string $reasonPhrase = ''): Response`

Retorna una **nueva instancia** con el código de estado HTTP especificado.

```php
$response = Response::getInstance();
$notFound = $response->withStatus(404, 'Not Found');
$created = $response->withStatus(201, 'Created');

// Cada instancia tiene su propio status code
```

**Validación**: Lanza `InvalidArgumentException` si el código no está entre 100-599.

### 2. `withHeader(string $name, $value): Response`

Retorna una **nueva instancia** con el header especificado.

```php
$response = Response::getInstance();
$modified = $response->withHeader('Content-Type', 'application/json');
```

### 3. `withAddedHeader(string $name, $value): Response`

Retorna una **nueva instancia** con el header **agregado**.

```php
$response = Response::getInstance();
$modified = $response
    ->withHeader('Cache-Control', 'public')
    ->withAddedHeader('Cache-Control', 'max-age=3600');
```

### 4. `withoutHeader(string $name): Response`

Retorna una **nueva instancia** sin el header especificado.

```php
$response = Response::getInstance();
$withoutCache = $response->withoutHeader('Cache-Control');
```

### 5. `withBody($body): Response`

Retorna una **nueva instancia** con el body especificado.

```php
$response = Response::getInstance();
$modified = $response->withBody(['success' => true, 'data' => $data]);
```

### 6. `withJson($data, int $status = 200): Response`

**Método de conveniencia** para crear respuestas JSON.

Retorna una **nueva instancia** con:
- Body codificado como JSON
- Header `Content-Type: application/json`
- Código de estado especificado

```php
$response = Response::getInstance();
$jsonResponse = $response->withJson(['message' => 'Success'], 201);

// Equivalente a:
$response
    ->withStatus(201)
    ->withHeader('Content-Type', 'application/json')
    ->withBody(['message' => 'Success']);
```

---

## Fluent Interface (Method Chaining)

Los métodos inmutables soportan **encadenamiento** (chaining) porque retornan instancias de `Request` o `Response`:

### Request Chaining

```php
$modified = Request::getInstance()
    ->withQueryParam('page', 1)
    ->withQueryParam('limit', 50)
    ->withHeader('Accept', 'application/json')
    ->withHeader('X-API-Key', 'secret-key')
    ->withBody(['filter' => 'active']);
```

### Response Chaining

```php
$response = Response::getInstance()
    ->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->withHeader('X-API-Version', '2.0')
    ->withHeader('Cache-Control', 'no-cache')
    ->withBody(['success' => true]);
```

### JSON Response Chaining

```php
$response = Response::getInstance()
    ->withJson(['data' => $result], 200)
    ->withHeader('X-Total-Count', $totalCount);
```

---

## Casos de Uso

### 1. Modificar Request sin Afectar el Original

```php
function processWithPagination(Request $request) {
    // Agregar paginación sin modificar el request original
    $paginatedRequest = $request
        ->withQueryParam('page', 1)
        ->withQueryParam('limit', 20);

    return $this->fetchData($paginatedRequest);
}

// El $request original no fue modificado
```

### 2. Crear Múltiples Variaciones de Response

```php
$baseResponse = Response::getInstance()
    ->withHeader('X-API-Version', '1.0');

// Crear diferentes respuestas a partir de la base
$successResponse = $baseResponse
    ->withJson(['success' => true], 200);

$errorResponse = $baseResponse
    ->withJson(['error' => 'Not found'], 404);

// $baseResponse permanece sin cambios
```

### 3. Middlewares Funcionales

```php
class AuthMiddleware {
    public function handle(Request $request) {
        $token = $request->getHeader('Authorization');

        if (!$token) {
            throw new UnauthorizedException();
        }

        $userId = $this->validateToken($token);

        // Retornar nuevo request con user_id agregado
        return $request->withQueryParam('authenticated_user_id', $userId);
    }
}
```

### 4. Testing sin Side Effects

```php
public function testControllerWithDifferentParams() {
    $baseRequest = Request::getInstance();

    // Test con page=1
    $request1 = $baseRequest->withQueryParam('page', 1);
    $result1 = $controller->index($request1);

    // Test con page=2 (sin afectar $request1)
    $request2 = $baseRequest->withQueryParam('page', 2);
    $result2 = $controller->index($request2);

    // Ambos tests son independientes
    $this->assertNotEquals($result1, $result2);
}
```

---

## Métodos Deprecados

Los siguientes métodos **mutables** están marcados como `@deprecated` y deberían evitarse en nuevo código:

### Request

| Método Deprecated | Reemplazo Inmutable |
|-------------------|---------------------|
| `shiftQuery($key)` | `withQueryParam()` / `withoutQueryParam()` |
| `shiftBodyParam($key)` | `withBody()` |
| `shiftHeader($key)` | `withHeader()` / `withoutHeader()` |

### Ejemplo de Migración

#### ❌ Código Viejo (Deprecated)

```php
$request = Request::getInstance();
$userId = $request->shiftQuery('user_id'); // MODIFICA $request
```

#### ✅ Código Nuevo (Recomendado)

```php
$request = Request::getInstance();
$userId = $request->get('user_id');

// Si necesitas removerlo, crea nueva instancia
$withoutUserId = $request->withoutQueryParam('user_id');
```

---

## Comparación con PSR-7

| Característica | SimpleRest (Fase 2) | PSR-7 |
|----------------|---------------------|-------|
| **Inmutabilidad** | ✅ `with*()` methods | ✅ `with*()` methods |
| **Tipo de retorno** | ✅ `self` | ✅ Interface types |
| **Singleton** | ⚠️ Soporta singleton | ❌ No usa singleton |
| **Backward Compat** | ✅ 100% compatible | ❌ Breaking change |
| **StreamInterface** | ❌ String/array | ✅ StreamInterface |

**Nota**: Para interoperabilidad completa con PSR-7, usa los [adaptadores PSR-7](./PSR-7.md).

---

## Mejores Prácticas

### ✅ DO: Usar métodos inmutables en nuevo código

```php
$modified = $request
    ->withQueryParam('sort', 'desc')
    ->withHeader('Accept', 'application/json');
```

### ✅ DO: Encadenar métodos para claridad

```php
$response = Response::getInstance()
    ->withStatus(201)
    ->withJson(['id' => $newId]);
```

### ✅ DO: Crear variaciones sin afectar originales

```php
$baseRequest = Request::getInstance();
$requestV1 = $baseRequest->withHeader('X-API-Version', '1.0');
$requestV2 = $baseRequest->withHeader('X-API-Version', '2.0');
```

### ❌ DON'T: Usar métodos deprecated

```php
// ❌ Evitar
$request->shiftQuery('user_id');

// ✅ Mejor
$userId = $request->get('user_id');
$withoutUserId = $request->withoutQueryParam('user_id');
```

### ❌ DON'T: Esperar que métodos `with*()` modifiquen la instancia original

```php
// ❌ Incorrecto
$request->withQueryParam('page', 2);
echo $request->get('page'); // null (no fue modificado)

// ✅ Correcto
$modified = $request->withQueryParam('page', 2);
echo $modified->get('page'); // 2
```

---

## Testing

### Ejemplo de Unit Test

```php
public function testImmutability() {
    $original = Request::getInstance();
    $modified = $original->withQueryParam('key', 'value');

    // Verificar que son instancias diferentes
    $this->assertNotSame($original, $modified);

    // Verificar que el original no fue modificado
    $this->assertNull($original->get('key'));

    // Verificar que el modificado tiene el cambio
    $this->assertEquals('value', $modified->get('key'));
}
```

---

## Migración desde Código Mutable

### Paso 1: Identificar uso de métodos deprecated

```bash
# Buscar uso de shiftQuery
grep -r "shiftQuery" app/
```

### Paso 2: Refactorizar gradualmente

```php
// ANTES
$userId = $request->shiftQuery('user_id');
$this->processUser($userId);

// DESPUÉS
$userId = $request->get('user_id');
$requestWithoutUserId = $request->withoutQueryParam('user_id');
$this->processUser($userId);
```

### Paso 3: Aprovechar inmutabilidad

```php
// Ahora puedes crear variaciones sin side effects
$requestA = $request->withQueryParam('type', 'A');
$requestB = $request->withQueryParam('type', 'B');

$resultsA = $this->process($requestA);
$resultsB = $this->process($requestB);
```

---

## Referencias

- [PSR-7: HTTP Message Interfaces](https://www.php-fig.org/psr/psr-7/)
- [Immutability in PHP](https://wiki.php.net/rfc/immutability)
- `docs/PSR-7.md` - Adaptadores PSR-7 de SimpleRest

---

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
