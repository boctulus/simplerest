# Sistema de Caché — SimpleRest

## Arquitectura

SimpleRest tiene un sistema de caché abstracto con **3 implementaciones** concretas:

```
Cache (abstracto)
├── FileCache       → Archivos en disco
├── DBCache         → Tabla en base de datos
└── InMemoryCache   → Memoria (por request)
```

**Interfaz**: `ICache`  
**Ubicación**: `src/framework/Libs/`

---

## FileCache

Almacena en archivos dentro de `storage/cache/`.

```php
use Boctulus\Simplerest\Libs\FileCache;

$cache = new FileCache();

// Guardar
$cache->set('users_active', $data, 3600);  // 1 hora TTL

// Obtener
$users = $cache->get('users_active');

// Eliminar
$cache->delete('users_active');

// Limpiar todo
$cache->flush();
```

## DBCache

Almacena en una tabla de base de datos.

```php
use Boctulus\Simplerest\Libs\DBCache;

$cache = new DBCache();
$cache->set('key', $value, 3600);
$result = $cache->get('key');
```

## InMemoryCache

Almacena en memoria durante la vida del request. Útil para evitar consultas repetidas.

```php
use Boctulus\Simplerest\Libs\InMemoryCache;

$cache = new InMemoryCache();
$cache->set('key', $value);
$result = $cache->get('key');
```

## Cache de Queries (Query Builder)

El Query Builder tiene integración directa con caché:

```php
// Resultado cacheado por 60 segundos
$users = DB::table('users')
    ->where('active', 1)
    ->cached(60)
    ->get();

// Cache por tiempo indefinido
$users = DB::table('users')
    ->cached()
    ->get();
```

## CLI

```bash
# Limpiar toda la caché
php com system clear
```

## Ver También

- [`QueryBuilder.md`](./QueryBuilder.md) — query caching
- [`Performance.md`](./Performance.md) — estrategias de optimización
- [`config/config.php`](../config/config.php) — configuración de caché
