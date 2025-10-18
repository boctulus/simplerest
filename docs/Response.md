# Clase Response

La clase `Response` implementa el patrón Singleton y proporciona una interfaz unificada para enviar respuestas HTTP estructuradas y consistentes.

## Tabla de contenidos
- [Instanciación](#instanciación)
- [Métodos principales](#métodos-principales)
- [Envío de respuestas](#envío-de-respuestas)
- [Manejo de errores](#manejo-de-errores)
- [Redirecciones](#redirecciones)
- [Formateo de respuestas](#formateo-de-respuestas)
- [Headers](#headers)
- [Paginación](#paginación)
- [Métodos de utilidad](#métodos-de-utilidad)
- [Patrones de uso](#patrones-de-uso)
- [Mejores prácticas](#mejores-prácticas)

## Instanciación

```php
// Forma explícita
$res = Response::getInstance();

// Usando helper (recomendado)
response()->send($data);

// Usando Factory (alternativa)
Factory::response()->send($data);
```

## Métodos principales

### Envío de respuestas

#### `send($data, $http_code = null)`
Envía una respuesta estructurada con datos y código HTTP.

```php
$res = Response::getInstance();

// Respuesta exitosa (200 por defecto)
$res->send($data);

// Con código específico
$res->send($data, 201); // Created

// Respuesta con error (≥400 llama a error() automáticamente)
$res->send('Not found', 404);
```

**Estructura de respuesta:**
```json
{
    "data": { ... },
    "status_code": 200,
    "error": []
}
```

#### `sendJson($data, $http_code = null, $error_msg = null)`
Envía respuesta como JSON explícitamente.

```php
response()->sendJson([
    'id' => 123,
    'name' => 'Product A'
], 200);
```

#### `sendCode($http_code)`
Envía solo un código de estado HTTP.

```php
response()->sendCode(204); // No Content
response()->sendCode(202); // Accepted
```

#### `sendOK()`
Envía código 200 sin datos adicionales.

```php
response()->sendOK();
```

### Manejo de errores

#### `error($error, $http_code = null, $detail = null, $location = null)`
Envía una respuesta de error y termina la ejecución.

```php
// Error simple
response()->error('No encontrado', 404);

// Con detalle
response()->error('Invalid data', 400, [
    'field' => 'email',
    'message' => 'Email format is invalid'
]);

// Error completo
response()->error('Forbidden', 403, 'User does not have permissions', 'UserController::delete');
```

**Estructura de error:**
```json
{
    "status": 404,
    "error": {
        "type": null,
        "code": null,
        "message": "No encontrado",
        "detail": null,
        "location": null
    }
}
```

**Helper global de error:**
```php
// Forma corta (recomendada)
error('Product not found', 404);

// Equivalente a
response()->error('Product not found', 404);
```

#### `formatError($error_msg, $error_code = null)`
Formatea un error para incluirlo en arrays de errores (método estático).

```php
$errors = [];

foreach ($product_ids as $pid) {
    $product = DB::table('products')->find($pid);

    if (!$product) {
        $errors[] = Response::formatError(
            "Product with ID=$pid not found",
            404
        );
        continue;
    }

    // Procesar producto...
}

return Response::format($data, 200, $errors);
```

**Respuesta con múltiples errores:**
```json
{
    "data": [ ... ],
    "status_code": 200,
    "error": [
        {
            "message": "Product with ID=123 not found",
            "code": 404
        },
        {
            "message": "Product with ID=456 not found",
            "code": 404
        }
    ]
}
```

### Redirecciones

#### `redirect($url, $http_code = 307)`
Realiza una redirección HTTP.

```php
// Redirección temporal (307)
response()->redirect('http://example.com');

// Redirección permanente (301)
response()->redirect('http://example.com', 301);

// Otros códigos soportados: 302, 307, 308
response()->redirect('/new-location', 302); // Found
response()->redirect('/moved', 308);        // Permanent Redirect
```

**Códigos HTTP de redirección:**
- `301`: Moved Permanently (permanente)
- `302`: Found (temporal)
- `307`: Temporary Redirect (temporal, preserva método HTTP)
- `308`: Permanent Redirect (permanente, preserva método HTTP)

### Formateo de respuestas

#### `format($data, $http_code = 200, $error_msg = '', $extra = [])`
Formatea una respuesta sin enviarla (método estático). Útil para retornar desde controladores.

```php
// Formato básico
return Response::format($products);

// Con código específico
return Response::format($user, 201); // Created

// Con errores
return Response::format($data, 200, $errors);

// Con datos extra (ej: paginación)
return Response::format($products, 200, '', [
    'paginator' => [
        'total' => 218,
        'page' => 1,
        'pageSize' => 20
    ]
]);
```

**Respuesta formateada:**
```json
{
    "data": [ ... ],
    "status_code": 200,
    "error": "",
    "paginator": {
        "total": 218,
        "page": 1,
        "pageSize": 20
    }
}
```

### Headers

#### `addHeader($header)` / `setHeader($header)`
Agrega un header HTTP.

```php
response()
    ->addHeader('X-Custom-Header: value')
    ->addHeader('X-Request-Id: ' . uniqid())
    ->send($data);
```

#### `addHeaders($headers)`
Agrega múltiples headers.

```php
response()->addHeaders([
    'X-Rate-Limit: 100',
    'X-Rate-Remaining: 99'
])->send($data);
```

### Códigos de estado

#### `code($http_code, $msg = '')`
Establece el código HTTP para la respuesta.

```php
response()
    ->code(201, 'Created')
    ->send($data);
```

### Paginación

#### `setPaginatorParams($row_count, $count, $current_page, $page_count, $page_size, $nextUrl)`
Configura parámetros de paginación automáticos.

```php
$total = 218;
$pageSize = 20;
$currentPage = 1;
$pageCount = ceil($total / $pageSize);

response()
    ->setPaginatorParams(
        $total,        // Total de registros
        count($data),  // Registros en esta página
        $currentPage,  // Página actual
        $pageCount,    // Total de páginas
        $pageSize,     // Tamaño de página
        "/api/products?page=2" // URL siguiente página
    )
    ->send($data);
```

### Métodos de utilidad

#### `set($data)`
Establece los datos a enviar (sin enviarlos aún).

```php
response()
    ->set($data)
    ->flush(); // Envía cuando estés listo
```

#### `get()`
Obtiene los datos establecidos.

```php
$currentData = response()->get();
```

#### `isEmpty()`
Verifica si la respuesta está vacía.

```php
if (response()->isEmpty()) {
    response([
        'message' => 'Default response'
    ]);
}
```

#### `flush()`
Envía la respuesta y termina la ejecución.

```php
response()
    ->set($data)
    ->addHeader('X-Custom: value')
    ->flush();
```

#### `encode()`
Fuerza la codificación JSON de la respuesta.

```php
response()
    ->encode()
    ->send($data);
```

#### `setPretty($state)`
Activa/desactiva el formateo "pretty" de JSON.

```php
response()
    ->setPretty(true)
    ->send($data);
```

#### `asObject($val = true)`
Configura si retornar datos como objeto.

```php
response()
    ->asObject(true)
    ->send($data);
```

#### `when($cond, $fn, ...$args)`
Ejecuta un callback condicionalmente.

```php
response()->when($isDev, function($res) {
    $res->setPretty(true);
})->send($data);
```

## Patrones de uso

### Patrón 1: Envío directo (no reutilizable)

**No recomendado para métodos que necesiten ser llamados desde otros lugares.**

```php
public function get($pid = null) {
    if (empty($pid)) {
        error('Parameter `pid` is required', 400);
    }

    $data = DB::table('products')->find($pid);

    // ❌ Cons: No setea HTTP status code, no es reutilizable
    Response::getInstance()->send($data);
}
```

### Patrón 2: Return directo (recomendado)

**El FrontController se encarga de estructurar la respuesta.**

```php
public function get($pid = null) {
    if (empty($pid)) {
        error('Parameter `pid` is required', 400);
    }

    $data = DB::table('products')->find($pid);

    // ✅ Pros: Setea HTTP status, estructura la respuesta, reutilizable
    return $data;
}
```

### Patrón 3: Response::format() (mejor opción)

**Estructurado, reutilizable y con control total.**

```php
public function get($pid = null) {
    if (empty($pid)) {
        error('Parameter `pid` is required', 400);
    }

    $data = DB::table('products')->find($pid);

    // ✅ Mejor: Control total, reutilizable, estructurado
    return Response::format($data, 200);
}
```

### Ejemplo completo: Manejo de múltiples errores

```php
function prices($product_ids = null, $user_id = null)
{
    $req = Request::getInstance();

    // Obtener datos (soporta POST y GET)
    if ($req->method() == 'POST') {
        $data = $req->getBodyDecoded();
        $product_ids = $data['product_ids'] ?? null;
        $user_id = $data['user_id'] ?? null;
    }

    // Normalizar a array
    if (!is_array($product_ids)) {
        $product_ids = explode(',', $product_ids);
    }

    // Validaciones
    if (empty($product_ids)) {
        error('Parameter `product_ids` is required', 400);
    }

    if (empty($user_id)) {
        error('Parameter `user_id` is required', 400);
    }

    // Procesar productos
    $data = [];
    $errors = [];

    foreach ($product_ids as $pid) {
        $product = DB::table('products')->find($pid);

        if (!$product) {
            $errors[] = Response::formatError(
                "Product with product_id=$pid not found",
                404
            );
            continue;
        }

        $price = $product['price'];
        $salePrice = $price * 0.8;

        $data[] = [
            'product_id' => $pid,
            'normal_price' => $price,
            'sale_price' => $salePrice
        ];
    }

    return Response::format($data, 200, $errors);
}
```

**Respuesta ejemplo:**
```json
{
    "data": [
        {
            "product_id": "123",
            "normal_price": 100,
            "sale_price": 80
        },
        {
            "product_id": "456",
            "normal_price": 200,
            "sale_price": 160
        }
    ],
    "status_code": 200,
    "error": [
        {
            "message": "Product with product_id=789 not found",
            "code": 404
        }
    ]
}
```

### Ejemplo: API con paginación

```php
function list()
{
    $req = Request::getInstance();

    $page = $req->get('page', 1);
    $pageSize = $req->get('pageSize', 20);
    $category = $req->get('category');

    // Consulta con filtros
    $query = DB::table('products');

    if ($category) {
        $query->where('category', $category);
    }

    // Total de registros
    $total = $query->count();

    // Datos paginados
    $products = $query
        ->limit($pageSize)
        ->offset(($page - 1) * $pageSize)
        ->get();

    // Formatear respuesta con paginación
    return Response::format($products, 200, '', [
        'paginator' => [
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'pages' => ceil($total / $pageSize)
        ]
    ]);
}
```

### Ejemplo: Redirección con ApiClient

**Sin seguir redirecciones:**
```php
$cli = new ApiClient($url);

$res = $cli->disableSSL()
    // ->followLocations() // NO seguir
    ->get()
    ->getResponse(false);

dd($cli->getStatus(), 'STATUS');       // 307
dd($cli->getHeaders(), 'HEADERS');     // Location: http://yahoo.com
dd($res, 'RES');
```

**Siguiendo redirecciones:**
```php
$cli = new ApiClient($url);

$res = $cli->disableSSL()
    ->followLocations() // ✅ Seguir redirecciones
    ->get()
    ->getResponse(false);

dd($cli->getStatus(), 'STATUS');   // 200 (de la URL final)
dd($res, 'RES');                   // Contenido de yahoo.com
```

## Mejores prácticas

### ✅ Recomendado

```php
// 1. Usar Response::format() para reutilización
return Response::format($data, 201);

// 2. Usar helper error() para errores
if (empty($user_id)) {
    error('User ID is required', 400);
}

// 3. Formatear errores múltiples consistentemente
$errors[] = Response::formatError("Error message", 404);

// 4. Usar códigos HTTP apropiados
return Response::format($newUser, 201);     // Created
return Response::format($updatedData, 200); // OK
response()->sendCode(204);                  // No Content

// 5. Retornar desde métodos (no enviar)
return Response::format($data);
```

### ❌ Evitar

```php
// 1. No retornar Response en sí (genera loop)
return Response::getInstance()->send($data); // ❌

// 2. No usar send() en métodos reutilizables
Response::getInstance()->send($data); // ❌ No reutilizable

// 3. No mezclar patrones
return $data; // ✅ O esto
Response::getInstance()->send($data); // ❌ No ambos
```

### Códigos HTTP comunes

**Éxito (2xx):**
- `200` OK - Respuesta exitosa estándar
- `201` Created - Recurso creado exitosamente
- `202` Accepted - Petición aceptada pero no procesada
- `204` No Content - Exitoso sin contenido de respuesta

**Redirección (3xx):**
- `301` Moved Permanently - Redirección permanente
- `302` Found - Redirección temporal
- `307` Temporary Redirect - Redirección temporal (preserva método)
- `308` Permanent Redirect - Redirección permanente (preserva método)

**Errores de cliente (4xx):**
- `400` Bad Request - Petición malformada
- `401` Unauthorized - No autenticado
- `403` Forbidden - Autenticado pero sin permisos
- `404` Not Found - Recurso no encontrado
- `422` Unprocessable Entity - Validación fallida

**Errores de servidor (5xx):**
- `500` Internal Server Error - Error del servidor
- `503` Service Unavailable - Servicio no disponible

## Notas importantes

1. **Singleton**: Solo hay una instancia de Response por petición
2. **Salida automática**: `send()`, `error()` y `flush()` terminan la ejecución con `exit`
3. **JSON automático**: La respuesta se codifica como JSON automáticamente si:
   - Se llama a `encode()`
   - Se llama a `sendJson()`
   - El header `Accept: application/json` está presente
4. **CLI**: Los métodos relacionados con headers no hacen nada en modo CLI
5. **Pretty print**: Se puede configurar globalmente en `config.php` o por petición con `setPretty()`
6. **Evitar data[data]**: La clase detecta y elimina anidaciones `data[data]` automáticamente

## Resumen de helpers globales

```php
// Enviar respuesta
response()->send($data);
response($data); // Alias

// Enviar error
error('Message', 404);
error('Message', 400, $details);

// Formatear respuesta
return Response::format($data, 200);

// Formatear error
$errors[] = Response::formatError("Error", 404);
```
