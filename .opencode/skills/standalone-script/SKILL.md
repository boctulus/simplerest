---
name: standalone-script
description: Pattern for creating standalone CLI scripts that bootstrap SimpleRest framework core to access DB, Query Builder, helpers, and libs without raw PDO.
---

# Standalone Script Skill

## Purpose

Avoid raw PDO in CLI/cron scripts by bootstrapping the framework core via `app.php`. This gives you access to `table()` (Query Builder), `DB`, all global helpers (`dd()`, `config()`, etc.), and all framework `Libs` classes.

## Canonical Boilerplate

```php
<?php declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\TemporaryExceptionHandler;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
    return;
}

require_once __DIR__ . '/app.php';

$handler = new TemporaryExceptionHandler();

try {
    // ── your code here ──

} catch (\Exception $e) {
    $handler->exception_handler($e);
}
```

## Key Elements

| Element | Purpose |
|---------|---------|
| `php_sapi_name() != "cli"` guard | Prevents HTTP execution; script only runs in CLI |
| `require_once __DIR__ . '/app.php'` | Boots framework: autoloader, Config, helpers, providers, DB connections |
| `TemporaryExceptionHandler` | Uses the framework `ExceptionHandler` trait so errors get proper formatting and logging |

## DB & Query Builder (No PDO)

### Default connection

```php
$users = table('users')->where('active', 1)->get();
dd($users);
```

### Specific connection (multi-tenant)

```php
DB::getConnection('zippy');

$cat = table('categories')
    ->whereNull('deleted_at')
    ->orderByRaw('RAND()')
    ->select('id')
    ->first();
```

### Transaction with connection

```php
DB::getConnection('woo3');
DB::beginTransaction();
try {
    table('orders')->create([...]);
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

### Switch and restore

```php
withConnection('woo3', function () {
    return table('posts')->get();
});
```

## Available Helpers (auto-loaded via `app.php`)

- `dd()`, `dump()` — debug
- `config()` — `Config::get()`
- `table()` — `DB::table()` / Query Builder
- `request()`, `response()`
- `setLang()`, `__()` / `_e()` — i18n
- `view()` — render views
- `withConnection()` — scoped connection switching

## Available Libs (namespace `Boctulus\Simplerest\Core\Libs`)

`DB`, `Strings`, `Arrays`, `Files`, `Date`, `Url`, `Cache`, `Validator`, `Schema`, `Config`, `EventBus`, `Mail`, `StdOut`, `Logger`, `Time`, `Session`, `Cookie`, `Image`, `ZipManager`, `ApiClient`, `Curl`, `Html`, `XML`, etc.

```php
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Files;

$slug = Strings::slugify('Hello World');
Files::append(LOGS_PATH . 'cron.log', "Done\n");
```

## Config Access

```php
$db_prefix = config("db_connections.{$id}.tb_prefix");
$debug     = config('debug');
```

## Rules & Pitfalls

1. **Always use `php_sapi_name()` guard** — the script must never execute in HTTP context.
2. **`app.php` must be required only once** — use `require_once`.
3. **`app.php` path** — it lives in the project root. If your script is in a subdirectory, adjust: `require_once __DIR__ . '/../app.php'`.
4. **`TemporaryExceptionHandler`** is the correct way to catch exceptions in standalone scripts; don't write raw `echo $e->getMessage()`.
5. **`table()` helper returns arrays** (not objects/Models like Laravel Eloquent).
6. **`table()` needs schema for `find()`** — without schema, use `where(['id' => $id])->first()` instead.
7. **Always call `DB::getConnection('id')` before queries** to select the correct database when using named connections.
8. **Scripts run outside request lifecycle** — `request()`, `response()` will have limited context; avoid them unless needed.
9. **For long-running cron jobs**, consider `set_time_limit(0)` and memory management.
