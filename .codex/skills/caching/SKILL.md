---
name: caching
description: Complete guide for the 3 cache implementations in SimpleRest — FileCache, DBCache, InMemoryCache — plus query caching and CLI cache management.
---

# Caching Skill

SimpleRest has 3 cache implementations sharing the `ICache` interface:

```
Cache (abstract)
├── FileCache       → disk files in storage/cache/
├── DBCache         → database table
└── InMemoryCache   → per-request memory
```

## Which Cache to Use

| Cache | Persistence | Speed | Best For |
|-------|------------|-------|----------|
| FileCache | Persistent (disk) | Medium | Long-lived data, cross-request |
| DBCache | Persistent (DB) | Slow | Shared across servers |
| InMemoryCache | Per-request | Fastest | Repeated same-request lookups |

## FileCache

```php
use Boctulus\Simplerest\Libs\FileCache;

$cache = new FileCache();

$cache->set('users_active', $data, 3600);   // 1 hour TTL
$users = $cache->get('users_active');         // retrieve
$cache->delete('users_active');              // single key
$cache->flush();                             // clear all
```

## DBCache

```php
use Boctulus\Simplerest\Libs\DBCache;

$cache = new DBCache();
$cache->set('key', $value, 3600);
$result = $cache->get('key');
```

## InMemoryCache

```php
use Boctulus\Simplerest\Libs\InMemoryCache;

$cache = new InMemoryCache();
$cache->set('key', $value);
$result = $cache->get('key');     // only within same request
```

## Query Builder Integration

```php
// Cached for 60 seconds
$users = DB::table('users')
    ->where('active', 1)
    ->cached(60)
    ->get();

// Cached indefinitely
$users = DB::table('users')
    ->cached()
    ->get();
```

## Cache Helper

```php
cache()->set('key', $data, 300);
$data = cache()->get('key');
```

## CLI Cache Management

```bash
php com system clear     # clear all cache
```

## Common Patterns

### Cache-Aside (Lazy Loading)
```php
function getExpensiveData() {
    $cache = new FileCache();
    $data  = $cache->get('expensive_key');

    if ($data === null) {
        $data = DB::table('products')
            ->where('active', 1)
            ->cached(300)
            ->get();
        $cache->set('expensive_key', $data, 3600);
    }

    return $data;
}
```

### Invalidation Strategy
- Time-based TTL (simplest)
- Manual delete on data change
- Use `cache_remember()` for auto-refresh

## Performance Tips

- Use InMemoryCache for repeated DB lookups in same request
- Use FileCache for API responses that change infrequently
- Avoid DBCache for high-frequency keys (defeats purpose)
- Set appropriate TTLs — too long = stale data, too short = no benefit

## See Also

- [`docs/Caching.md`](../docs/Caching.md) — full caching reference
- [`docs/Performance.md`](../docs/Performance.md) — optimization strategies
- `query-builder` skill — `cached()` method on queries
