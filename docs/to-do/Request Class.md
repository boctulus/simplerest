# Clase Request

La clase `Request` implementa el patrón Singleton y proporciona una interfaz unificada para acceder a datos de peticiones HTTP y CLI.

## Instanciación

```php
$req = Request::getInstance();
```

## Métodos principales

### Query String / CLI Options

#### `get($key, $default = null)`
Obtiene un valor del query string (HTTP) o de opciones CLI.

```php
$limit  = $req->get('limit', 10);
$offset = $req->get('offset', 0);
$order  = $req->get('order', []);
```

#### `has($key)`
Verifica si existe una clave en el query string o en las opciones CLI.

```php
if ($req->has('sku')) {
    $skus = explode(',', $req->get('sku'));
    $filters['sku'] = $skus;
}

if ($req->has('search')) {
    $query = $req->get('search');
}
```

#### `shiftQuery($key, $default = null, $fn = null)`
Obtiene un valor del query string y lo elimina (getter destructivo). Útil para procesamiento secuencial de parámetros.

```php
$token = $req->shiftQuery('token');
$api_key = $req->shiftQuery('api_key');
```

Opcionalmente acepta una función de transformación:

```php
$limit = $req->shiftQuery('limit', 10, function($value) {
    return (int) $value;
});
```

### Body / Payload

#### `getBody(?bool $as_obj = null)`
Obtiene el cuerpo de la petición. Retorna array u objeto según configuración.

```php
// Como array
$data = $req->as_array()->getBody();

// Como objeto (por defecto)
$data = $req->getBody();
```

#### `getBodyDecoded()`
Obtiene el cuerpo de la petición y lo decodifica según el Content-Type.

```php
$data = $req->getBodyDecoded();
// Decodifica JSON o form-urlencoded automáticamente
```

#### `getBodyParam($key)`
Obtiene un parámetro específico del body.

```php
$username = $req->getBodyParam('username');
$password = $req->getBodyParam('password');
```

#### `shiftBodyParam($key)`
Obtiene y elimina un parámetro del body (getter destructivo).

```php
$user_id = $req->shiftBodyParam('user_id');
$product_ids = $req->shiftBodyParam('product_ids');
```

### Parámetros unificados: getOption()

#### `getOption($key, $default = null)`
Método universal que busca un parámetro en el siguiente orden:
1. Query string (`?key=value` en HTTP o `--key=value` en CLI)
2. Body (JSON o form-data)
3. Parámetros de ruta
4. Headers

```php
// Busca 'name' en query, body, params o headers
$name = $req->getOption('name');

// Con valor por defecto
$limit = $req->getOption('limit', 50);

// Acepta alias cortos
$slug = $req->getOption('slug') ?? $req->getOption('s');
```

**Casos de uso comunes:**

```php
// En un controlador CLI
// Comando: php com zippycart category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
function create_category() {
    $req = Request::getInstance();

    $name = $req->getOption('name');     // "Leche y derivados"
    $slug = $req->getOption('slug');     // "dairy.milk"
    $parent = $req->getOption('parent'); // "dairy"
}

// En un controlador HTTP (API)
function update_product() {
    $req = Request::getInstance();

    // Funciona tanto con query string como con body JSON
    $product_id = $req->getOption('id');
    $price = $req->getOption('price');
}
```

#### `input($key, $default = null)`
Alias de `getOption()`.

```php
$email = $req->input('email');
$password = $req->input('password', '');
```

### Diferencias: get() vs getOption()

Es importante entender cuándo usar `get()` y cuándo usar `getOption()`.

#### `get()` - Específico y rápido

**Busca SOLO en query string / opciones CLI:**

```php
// Solo accede a static::$query_arr
return static::$query_arr[$key] ?? $default_value;
```

**Ventajas:**
- ✅ Acceso directo, muy rápido
- ✅ Específico y predecible
- ✅ Ideal cuando sabes exactamente de dónde viene el dato

**Ejemplo:**
```php
// URL: /api/products?limit=10
$limit = $req->get('limit'); // "10" ✅

// CLI: php com category list --limit=10
$limit = $req->get('limit'); // "10" ✅

// Body JSON: {"limit": 10}
$limit = $req->get('limit'); // null ❌ (no busca en body)
```

#### `getOption()` - Universal y flexible

**Busca en MÚLTIPLES lugares (en orden):**

1. Query string (`?key=value` o `--key=value`)
2. Body (JSON o form-data)
3. Parámetros de ruta
4. Headers

**Ventajas:**
- ✅ Funciona en múltiples contextos
- ✅ Código reutilizable entre HTTP y CLI
- ✅ No necesitas saber de dónde viene el dato
- ✅ Incluye caché interno para mejor rendimiento

**Ejemplo:**
```php
// Query string: /api/products?limit=10
$limit = $req->getOption('limit'); // "10" ✅

// Body JSON: {"limit": 10}
$limit = $req->getOption('limit'); // 10 ✅

// Header: X-Limit: 10
$limit = $req->getOption('limit'); // "10" ✅

// Route param: /products/:limit → /products/10
$limit = $req->getOption('limit'); // "10" ✅
```

#### Tabla comparativa

| Aspecto | `get()` | `getOption()` |
|---------|---------|---------------|
| **Scope** | Solo query string/CLI | Query + Body + Params + Headers |
| **Performance** | Más rápido (acceso directo) | Más lento (busca en 4 lugares) |
| **Cache** | No | Sí (cache interno) |
| **Uso típico** | Cuando sabes de dónde viene | Cuando no sabes la fuente |
| **Especificidad** | Alta | Baja (más flexible) |
| **Redundancia** | No, `getOption()` usa `get()` internamente |

#### Cuándo usar cada uno

**Usa `get()` cuando:**

```php
// ✅ Sabes que viene del query string
function list() {
    $page = $req->get('page', 1);       // Paginación siempre en URL
    $search = $req->get('search');      // Búsqueda siempre en URL

    // Procesar filtros de URL
    if ($req->has('category')) {
        $filters['category'] = $req->get('category');
    }
}

// ✅ APIs tradicionales con parámetros en URL
function search() {
    $query = $req->get('q');
    $filters = $req->get('filters', []);
}
```

**Usa `getOption()` cuando:**

```php
// ✅ Código que funciona tanto en HTTP como CLI
function create_product() {
    // Funciona con query string, body, CLI options
    $name = $req->getOption('name');
    $price = $req->getOption('price');
}

// ✅ API flexible que acepta params de varias fuentes
function update($id = null) {
    // Puede venir de query, body, o route param
    $id = $req->getOption('id');
    $name = $req->getOption('name');
}

// ✅ No sabes si el cliente envía por query o body
function authenticate() {
    // API key puede venir de query, header, o body
    $apiKey = $req->getOption('api_key');
}
```

#### getOption() versus get()

`getOption()` **usa** `get()` internamente como primer paso:

```php
function getOption(string $key, $default = null) {
    // 1. Primero intenta get() (query string)
    if ($this->has($key)) {
        $value = $this->get($key);  // ← Usa get() aquí
    }

    // 2. Si no encuentra, busca en body
    if ($value === null && is_array(static::$body)) {
        $value = static::$body[$key];
    }

    // 3. Luego en params de ruta
    // 4. Finalmente en headers

    return $value ?? $default;
}
```

- `get()` = específico, rápido, predecible
- `getOption()` = flexible, universal, conveniente

#### Ejemplo comparativo

```php
// Controlador API HTTP (específico)
function listProducts() {
    // ✅ Usa get() - sabemos que es query string
    $page = $req->get('page', 1);
    $limit = $req->get('limit', 20);
    $category = $req->get('category');

    // ...
}

// Controlador universal (HTTP + CLI)
function createCategory() {
    // ✅ Usa getOption() - funciona en ambos contextos
    $name = $req->getOption('name');    // query, body, o --name
    $slug = $req->getOption('slug');    // query, body, o --slug

    // ...
}

// API flexible con múltiples fuentes
function authenticate() {
    // ✅ Usa getOption() - busca en todas partes
    $apiKey = $req->getOption('api_key');  // query, body, header, o --api-key

    if (empty($apiKey)) {
        error('API key required', 401);
    }

    // ...
}
```

### Headers

#### `header($key)` / `getHeader($key)`
Obtiene un header específico.

```php
$contentType = $req->getHeader('Content-Type');
$auth = $req->getHeader('Authorization');
$apiKey = $req->getHeader('X-Api-Key');
```

#### `headers()`
Obtiene todos los headers.

```php
$allHeaders = $req->headers();
```

#### `shiftHeader($key)`
Obtiene y elimina un header (getter destructivo).

```php
$acceptEncoding = $req->shiftHeader('Accept-Encoding');
```

### Autenticación

#### `getAuth()`
Obtiene el token de autorización (Bearer token).

```php
$token = $req->getAuth();
// Retorna: "Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

#### `hasAuth()`
Verifica si la petición incluye autorización.

```php
if ($req->hasAuth()) {
    $token = $req->getAuth();
    // Validar token...
}
```

#### `getApiKey()`
Obtiene la API key desde header `X-Api-Key` o query string.

```php
$apiKey = $req->getApiKey();
```

#### `hasApiKey()`
Verifica si existe una API key.

```php
if ($req->hasApiKey()) {
    $key = $req->getApiKey();
    // Validar API key...
}
```

#### `authMethod()`
Determina el método de autenticación usado.

```php
$method = $req->authMethod();
// Retorna: 'API_KEY', 'JWT', o null
```

#### `isAuthenticated()`
Verifica si la petición está autenticada (por API key o JWT).

```php
if (!$req->isAuthenticated()) {
    error('Unauthorized', 401);
}
```

### Multi-tenant

#### `getTenantId()`
Obtiene el ID del tenant desde header `X-Tenant-Id` o query string.

```php
$tenantId = $req->getTenantId();
```

#### `hasTenantId()`
Verifica si existe un tenant ID.

```php
if ($req->hasTenantId()) {
    DB::setConnection('tenant_' . $req->getTenantId());
}
```

### Métodos HTTP

#### `method()`
Obtiene el método HTTP usado (GET, POST, PUT, DELETE, etc.).

```php
$method = $req->method();

if ($method == 'POST') {
    // Procesar creación
} elseif ($method == 'PUT') {
    // Procesar actualización
}
```

Soporta method override mediante:
- Query string: `?_method=PUT`
- Header: `X-HTTP-Method-Override: PUT`

### Form Data

#### `getFormData()`
Obtiene datos de formulario ($_POST).

```php
$formData = $req->getFormData();
```

#### `parseFormData()`
Parsea form data, incluyendo JSON enviado como `application/x-www-form-urlencoded`.

```php
$data = $req->parseFormData();
```

### Encoding

#### `acceptEncoding()`
Obtiene el encoding aceptado por el cliente.

```php
$encoding = $req->acceptEncoding();
// Retorna: "gzip, deflate, br"
```

#### `gzip()`
Verifica si el cliente acepta gzip.

```php
if ($req->gzip()) {
    // Comprimir respuesta
}
```

#### `deflate()`
Verifica si el cliente acepta deflate.

```php
if ($req->deflate()) {
    // Usar deflate
}
```

### Detección de contexto

#### `isAjax()`
Verifica si es una petición AJAX.

```php
if ($req->isAjax()) {
    return json_encode($data);
} else {
    return view('template', $data);
}
```

#### `isBrowser()`
Verifica si la petición viene de un navegador (estático).

```php
if (Request::isBrowser()) {
    // Mostrar HTML
} else {
    // Retornar JSON
}
```

### Información adicional

#### `ip()`
Obtiene la IP del cliente (estático).

```php
$ip = Request::ip();
// Considera X-Forwarded-For si está presente
```

#### `user_agent()`
Obtiene el User-Agent del cliente (estático).

```php
$userAgent = Request::user_agent();
```

### Paginación

#### `getPaginatorParams()`
Obtiene parámetros de paginación según configuración.

```php
$paginatorParams = $req->getPaginatorParams();
// Retorna: ['page' => 1, 'pageSize' => 20]
```

### Parámetros de ruta

#### `setParams($params)`
Establece parámetros de ruta (usado internamente por el router).

```php
$req->setParams(['id' => 123, 'slug' => 'my-product']);
```

#### `getParam($index)`
Obtiene un parámetro por índice.

```php
$id = $req->getParam(0);
```

#### `getParams()`
Obtiene todos los parámetros.

```php
$params = $req->getParams();
```

### JSON

#### `json()`
Alias de `getBodyDecoded()`.

```php
$data = $req->json();
```

### Código de respuesta

#### `getCode()`
Obtiene el código HTTP actual.

```php
$code = $req->getCode();
```

## Soporte CLI

La clase Request automáticamente detecta si está en modo CLI y parsea opciones del estilo:

```bash
php com zippycart category create --name="Lácteos" --slug=dairy --parent=food
```

Los parámetros CLI son accesibles mediante `get()`, `has()` y `getOption()`:

```php
$name = $req->getOption('name');     // "Lácteos"
$slug = $req->getOption('slug');     // "dairy"
$parent = $req->getOption('parent'); // "food"
```

**Formatos soportados:**
- `--key=value` → `$req->get('key')` retorna `"value"`
- `--key:value` → `$req->get('key')` retorna `"value"`
- `--key` → `$req->get('key')` retorna `true` (flag booleano)
- `--dry-run` → `$req->get('dry_run')` retorna `true` (los guiones se convierten en underscores)

## ArrayAccess

La clase implementa `ArrayAccess` sobre los parámetros de ruta:

```php
$req['id'] = 123;
$id = $req['id'];
isset($req['id']);
unset($req['id']);
```

## Ejemplo completo: Controlador API

```php
use Boctulus\Simplerest\Core\Request;

class ProductController extends Controller
{
    function create() {
        $req = Request::getInstance();

        // Validar autenticación
        if (!$req->isAuthenticated()) {
            error('Unauthorized', 401);
        }

        // Obtener datos del producto
        $name = $req->getOption('name');
        $price = $req->getOption('price');
        $category = $req->getOption('category');

        // Validaciones
        if (empty($name)) {
            error('Product name is required', 400);
        }

        if (empty($price)) {
            error('Product price is required', 400);
        }

        // Datos opcionales
        $description = $req->getOption('description', '');
        $sku = $req->getOption('sku', null);

        // Crear producto
        $product_id = DB::table('products')->insert([
            'name' => $name,
            'price' => $price,
            'category' => $category,
            'description' => $description,
            'sku' => $sku,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return Response::format([
            'id' => $product_id,
            'name' => $name,
            'price' => $price
        ], 201);
    }

    function list() {
        $req = Request::getInstance();

        // Paginación
        $page = $req->get('page', 1);
        $pageSize = $req->get('pageSize', 20);

        // Filtros
        $category = $req->get('category');
        $search = $req->get('search');

        $query = DB::table('products');

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        $products = $query
            ->limit($pageSize)
            ->offset(($page - 1) * $pageSize)
            ->get();

        return Response::format($products);
    }
}
```

## Ejemplo completo: Comando CLI

```php
use Boctulus\Simplerest\Core\Request;

class CategoryController extends Controller
{
    function create_category() {
        $req = Request::getInstance();

        // Obtener opciones
        $name = $req->getOption('name');
        $slug = $req->getOption('slug');
        $parent = $req->getOption('parent');
        $image_url = $req->getOption('image_url');

        // Validaciones
        if (empty($name)) {
            dd(['error' => 'Missing --name'], 'Create category');
            return;
        }

        // Auto-generar slug si no se proporciona
        if (empty($slug)) {
            $slug = Strings::normalize($name);
        }

        // Crear categoría
        $id = uniqid('cat_');

        DB::table('categories')->insert([
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'parent_slug' => $parent,
            'image_url' => $image_url,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        dd([
            'ok' => true,
            'id' => $id,
            'slug' => $slug,
            'name' => $name
        ], 'Created category');
    }
}
```

**Uso:**
```bash
php com category create_category --name="Lácteos" --slug=dairy --parent=food
```

## Tips y mejores prácticas

### Selección de métodos

1. **Usar `get()` cuando sepas la fuente**: Si sabes que el parámetro viene del query string, usa `get()` por rendimiento
   ```php
   $page = $req->get('page', 1);        // ✅ Paginación siempre en URL
   $search = $req->get('search');       // ✅ Búsqueda siempre en URL
   ```

2. **Usar `getOption()` para flexibilidad**: Para código universal (HTTP + CLI) o cuando no sabes la fuente
   ```php
   $name = $req->getOption('name');     // ✅ Funciona en query, body, CLI
   $apiKey = $req->getOption('api_key'); // ✅ Busca en query, body, headers
   ```

3. **No mezclar sin razón**: Si estás usando `get()` en un método, mantén la consistencia
   ```php
   // ✅ Consistente
   $page = $req->get('page', 1);
   $limit = $req->get('limit', 20);

   // ❌ Inconsistente sin razón
   $page = $req->get('page', 1);
   $limit = $req->getOption('limit', 20);
   ```

### Validación y seguridad

4. **Validar siempre la entrada del usuario**: No confiar en datos sin validar
   ```php
   $email = $req->getOption('email');
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       error('Invalid email format', 400);
   }
   ```

5. **Usar valores por defecto apropiados**: Preferir valores por defecto en el método
   ```php
   $limit = $req->get('limit', 10);     // ✅ Mejor
   $limit = $req->get('limit') ?? 10;   // ❌ Menos claro
   ```

6. **Aprovechar `has()` antes de procesar**: Evita warnings y hace el código más robusto
   ```php
   if ($req->has('filters')) {
       $filters = json_decode($req->get('filters'), true);
   }
   ```

### Contexto y convenciones

7. **En CLI, usar nombres descriptivos**: Los nombres largos son más claros que alias cortos
   ```php
   // ✅ Claro
   php com import --dry-run --verbose

   // ❌ Confuso
   php com import -d -v
   ```

8. **Considerar multi-tenancy**: Usar `getTenantId()` cuando la aplicación lo soporte
   ```php
   if ($req->hasTenantId()) {
       DB::setConnection('tenant_' . $req->getTenantId());
   }
   ```

9. **Verificar autenticación apropiadamente**: Elegir el método según el tipo de auth
   ```php
   // API con JWT
   if (!$req->hasAuth()) {
       error('Unauthorized', 401);
   }

   // API con API Key
   if (!$req->hasApiKey()) {
       error('API key required', 401);
   }

   // Cualquier método de autenticación
   if (!$req->isAuthenticated()) {
       error('Authentication required', 401);
   }
   ```

### Performance

10. **Evitar llamadas repetidas**: Asignar a variable si usas un valor múltiples veces
    ```php
    // ❌ Busca 3 veces
    if ($req->getOption('cache') === 'redis') {
        $cache = new Redis($req->getOption('cache'));
        log('Using cache: ' . $req->getOption('cache'));
    }

    // ✅ Busca 1 vez (getOption tiene caché, pero mejor ser explícito)
    $cacheType = $req->getOption('cache');
    if ($cacheType === 'redis') {
        $cache = new Redis($cacheType);
        log('Using cache: ' . $cacheType);
    }
    ```
