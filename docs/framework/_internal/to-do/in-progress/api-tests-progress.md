# Progress: API Unit Tests - Collections & TrashCan

**Fecha:** 2026-01-29
**Estado:** En progreso - Parcialmente completado

## Resumen

Se solicitó implementar y corregir tests para los endpoints `/api/v1/collections` y `/api/v1/trash_can`, además de verificar que `tests/ApiTest.php` pase completamente.

## Estado de Tests

### ✅ ApiCollectionsTest.php
**Estado:** ✅ COMPLETADO - 4/4 tests pasando

### ⚠️ ApiTrashCanTest.php
**Estado:** 🔴 ROTO - 3/4 tests pasando, 1 falla
**Bug:** testSoftDeleteAndTrashCan - Ver sección "BUGS DETECTADOS"

### 🔄 ApiTest.php
**Estado:** 🔄 EN PROGRESO - No completado

---

## Tareas Completadas ✅

### 1. ApiCollectionsTest.php (Completado anteriormente)

**Problemas encontrados y solucionados:**
- ApiClient no estaba decodificando respuestas JSON (`->decode()` faltante)
- `setJWTAuth()` tenía formato incorrecto de headers
- Tests no usaban `BASE_URL` correctamente
- Login no retornaba `uid` correctamente

**Archivos modificados:**
- `tests/ApiCollectionsTest.php`

**Solución:**
- Usar `->addHeader('Authorization', "Bearer {$token}")` en vez de `setJWTAuth()`
- Agregar `->decode()` antes de `getDataOrFail()`
- Corregir obtención de `uid` desde respuesta de login

### 2. ApiTrashCanTest.php (Parcialmente completado - Bug detectado)
**Estado inicial:** ✅ 4/4 tests pasando
**Estado actual:** 🔴 3/4 tests pasando - testSoftDeleteAndTrashCan ROTO (ver BUGS DETECTADOS)

**Problemas encontrados y solucionados:**

#### a) Conexión y modelo
- TrashCan.php no usaba `get_model_name()` helper para manejar conexiones/tenants
- `$this->instance2` nunca se inicializaba (causaba error "Call to member function showDeleted() on null")

**Solución en `src/framework/Api/TrashCan.php`:**
```php
// Línea 37: Usar get_model_name() helper
$this->model = get_model_name($this->table_name, $this->tenantid);

// Línea 52: Inicializar instance2
$this->instance2 = (new $this->model())->assoc();
```

#### b) Método showDeleted() faltante
- QueryBuilderTrait no tenía método `showDeleted()`

**Solución en `src/framework/Traits/QueryBuilderTrait.php`:**
```php
// Agregar método showDeleted() como alias de deleted(true)
function showDeleted()
{
    return $this->deleted(true);
}
```

#### c) ProductsModel no cargaba schema
- Model constructor no estaba cargando el schema explícitamente

**Solución en `app/Models/main/ProductsModel.php`:**
```php
function __construct(bool $connect = false, $schema = null, bool $load_config = true){
    if ($schema === null) {
        $schema = ProductsSchema::class;
    }
    parent::__construct($connect, $schema, $load_config);
}
```

#### d) Paginación en trash_can
- El endpoint retorna solo 10 items por defecto
- Tests con IDs altos no aparecían en primera página

**Solución:**
- Agregar `&limit=100` a GET requests en tests

#### e) Tests creaban productos ya soft-deleted
- Crear producto con `deleted_at` y luego intentar borrarlo causaba 404
- El endpoint normal no encuentra productos ya borrados

**Solución:**
- Crear productos SIN `deleted_at`
- Dejar que el endpoint DELETE los borre (soft delete)

#### f) PATCH para undelete retornaba 404
- ApiController creaba instancias nuevas sin `showDeleted()`
- No podía encontrar productos soft-deleted para restaurar

**Solución en `src/framework/Api/TrashCan.php`:**
```php
// Agregar override de getModelInstance()
protected function getModelInstance($fetch_mode = 'ASSOC', bool $reuse = false){
    $instance = parent::getModelInstance($fetch_mode, $reuse);
    $instance->showDeleted();
    return $instance;
}

// Agregar hook onPuttingBeforeCheck()
protected function onPuttingBeforeCheck($id, &$data){
    $this->instance->showDeleted();
    $this->instance2->showDeleted();
}
```

#### g) Formato de respuesta
- Tests esperaban `{success: true}` pero endpoints retornan `{data: "OK"}` o `{data: {deleted_at: null}}`

**Solución:**
- Actualizar assertions en tests para validar formato correcto

**Archivos modificados:**
- `tests/ApiTrashCanTest.php`
- `src/framework/Api/TrashCan.php`
- `src/framework/Traits/QueryBuilderTrait.php`
- `app/Models/main/ProductsModel.php`

### 3. ApiTest.php
**Estado:** 🔄 EN PROGRESO - Requiere grupo `refactor`

**Observaciones:**
- Los tests están bajo `@group refactor`
- Debe ejecutarse con: `./vendor/bin/phpunit --bootstrap vendor/autoload.php --group refactor tests/ApiTest.php`
- Tiene aproximadamente 30 test cases
- En ejecución inicial se detectaron 2 errores (tests individuales pasan: `testgetme` ✅)
- La ejecución completa toma tiempo considerable (>2 min)

**Estado actual:**
- No completado - ejecución en progreso al momento de guardar contexto
- Requiere análisis de errores específicos cuando termine

## Próximos Pasos 📋

### Para ApiTest.php:

1. **Ejecutar tests completos:**
```bash
./vendor/bin/phpunit --bootstrap vendor/autoload.php --group refactor tests/ApiTest.php
```

2. **Identificar tests que fallan:**
   - Capturar output completo con errores
   - Analizar cada error específico

3. **Posibles problemas a revisar:**
   - Mismo patrón de ApiClient (decode, headers, etc.)
   - Formato de respuestas esperadas vs actuales
   - Credenciales de usuario de prueba
   - Datos de prueba requeridos en BD

4. **Corregir tests individualmente:**
   - Usar `--filter testNombre` para tests específicos
   - Validar con CURL manual si es necesario
   - Aplicar mismas soluciones que en CollectionsTest/TrashCanTest

## Comandos Útiles

```bash
# Ejecutar todos los tests de Collections
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ApiCollectionsTest.php

# Ejecutar todos los tests de TrashCan
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ApiTrashCanTest.php

# Ejecutar ApiTest completo
./vendor/bin/phpunit --bootstrap vendor/autoload.php --group refactor tests/ApiTest.php

# Ejecutar un test específico de ApiTest
./vendor/bin/phpunit --bootstrap vendor/autoload.php --group refactor --filter testgetme tests/ApiTest.php

# Verificar productos soft-deleted en BD
php com sql select "SELECT id, name, belongs_to, deleted_at FROM products WHERE belongs_to = 66 AND deleted_at IS NOT NULL ORDER BY id DESC LIMIT 5" --connection=main

# Verificar credenciales de usuario de prueba
# Email: tester3@g.c
# Password: gogogo
# UID: 66
```

## Lecciones Aprendidas 💡

1. **ApiClient usage pattern:**
   ```php
   $client = new ApiClient();
   $res = $client
       ->addHeader('Authorization', "Bearer {$token}")
       ->addHeader('Content-Type', 'application/json')
       ->setBody($data)
       ->decode()  // IMPORTANTE: siempre antes de getDataOrFail()
       ->post(BASE_URL . 'api/v1/endpoint')
       ->getDataOrFail();
   ```

2. **TrashCan debe usar get_model_name()** para soportar multi-tenant correctamente

3. **QueryBuilder instances necesitan showDeleted()** para trabajar con soft-deleted records

4. **Tests deben usar límites apropiados** al paginar resultados (default es 10 items)

5. **Formato de respuestas API:**
   - DELETE: `{data: "OK"}`
   - PATCH undelete: `{data: {deleted_at: null}}`
   - No usar `{success: true}` en assertions

## ⚠️ BUGS DETECTADOS - PENDIENTE DE REPARACIÓN

### Bug #1: testSoftDeleteAndTrashCan falla - Producto no aparece en trash_can

**Fecha de detección:** 2026-01-29 (después de implementar correcciones)

**Estado:** 🔴 ROTO - 1/4 tests fallando en ApiTrashCanTest

**Síntomas:**
```bash
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ApiTrashCanTest.php

There was 1 failure:
1) testSoftDeleteAndTrashCan
Product was not found in trash can after soft delete
Failed asserting that false is true.
Line: 134
```

**Descripción del problema:**
- El test `testSoftDeleteAndTrashCan` crea un producto directamente con `deleted_at` establecido
- Luego consulta el endpoint `GET /api/v1/trash_can?entity=Products&limit=100`
- El producto creado NO aparece en los resultados del trash_can

**Evidencia:**
```bash
# Se crea producto ID: 100000131 con deleted_at
# Trash_can retorna 50 items
# Últimos IDs en trash_can: 100000112, 100000120, 100000121, 100000124, 100000127
# Producto 100000131 NO está en la lista
```

**Posibles causas:**
1. El override de `getModelInstance()` en TrashCan.php podría estar afectando el GET
2. Hay algún filtro adicional no considerado en el GET
3. Problema de ordenamiento o límite real vs esperado
4. La query del GET podría tener algún WHERE adicional que excluye productos nuevos

**Contexto del código afectado:**

`src/framework/Api/TrashCan.php` - línea ~66-73:
```php
// Override agregado para soportar PATCH/undelete
protected function getModelInstance($fetch_mode = 'ASSOC', bool $reuse = false){
    $instance = parent::getModelInstance($fetch_mode, $reuse);
    $instance->showDeleted();
    return $instance;
}
```

`src/framework/Api/TrashCan.php` - línea ~74-78 (get method):
```php
function get($id = null) {
    if (!$this->instance->inSchema([$this->instance->belongsTo()]) && !$this->acl->hasSpecialPermission('read_all_trashcan')){
        error("Forbidden", 403);
    }
    parent::get($id);
}

protected function onGettingAfterCheck($id){
    $this->instance
    ->showDeleted()
    ->where([$this->instance->deletedAt(), NULL, 'IS NOT']);
}
```

**Archivos involucrados:**
- `tests/ApiTrashCanTest.php` (líneas 91-151)
- `src/framework/Api/TrashCan.php` (métodos: get, onGettingAfterCheck, getModelInstance)
- `src/framework/Api/ApiController.php` (parent get method)

**Pasos para reproducir:**
```bash
# 1. Ejecutar el test
./vendor/bin/phpunit --bootstrap vendor/autoload.php --filter testSoftDeleteAndTrashCan tests/ApiTrashCanTest.php

# 2. Debug manual (crear producto y verificar)
php -r "
require_once 'vendor/autoload.php';
require_once 'app.php';
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\DB;
\$config = Boctulus\Simplerest\Core\Libs\Config::get();
define('BASE_URL', rtrim(\$config['app_url'], '/') . '/');

// Login como tester3@g.c
// Crear producto con deleted_at
// Consultar trash_can con limit=100
// Verificar si el producto creado está en la lista
"
```

**Próximos pasos para reparar:**
1. Investigar si `getModelInstance()` override está causando side effects en GET
2. Revisar flujo completo del GET en TrashCan vs ApiController
3. Verificar si hay filtros adicionales (belongs_to, workspace, etc) que excluyen el producto
4. Considerar si el override debe ser condicional (solo para PATCH, no para GET)
5. Debug con logs en `onGettingAfterCheck()` para ver query exacta

**Workaround temporal:**
- Ninguno - el test debe pasar correctamente

**Prioridad:** 🔴 ALTA - Rompe suite de tests existente

---

## Referencias

- **Prompt original:** (ver `prompts/` si existe referencia)
- **Credenciales:** `docs/login-credentials.md`
- **Tests relacionados:**
  - `tests/ApiCollectionsTest.php` ✅
  - `tests/ApiTrashCanTest.php` ✅
  - `tests/ApiTest.php` 🔄

