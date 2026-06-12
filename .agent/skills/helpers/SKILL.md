---
name: helpers
description: Reference for all 21 global helper files in SimpleRest — available without use/require, covering debug, auth, DB, i18n, routing, config, and more.
---

# Helpers Skill

21 helper files auto-loaded via `app.php`. Available globally — no `use` or `require` needed.

## Full Helper List

| File | Functions |
|------|-----------|
| `cache.php` | `cache()`, `cache_remember()` |
| `config.php` | `config()`, `config_path()` |
| `date.php` | `now()`, `today()`, `format_date()` |
| `db.php` | `db()`, `table()`, `withDefaultConnection()` |
| `debug.php` | `dd()`, `dump()`, `debug()` |
| `env.php` | `env()`, `env_exists()` |
| `etc.php` | Misc utilities |
| `factories.php` | `auth()`, `acl()`, `get_user_model_name()` |
| `gdrive.php` | `gdrive()` |
| `html.php` | `html()`, `script()`, `style()` |
| `http.php` | `redirect()`, `back()`, `cors()` |
| `i18n.php` | `__()`, `_e()`, `trans()` |
| `loggers.php` | `logger()`, `log_error()` |
| `options.php` | `option()`, `update_option()` |
| `package.php` | `register_package()`, `discover_packages()` |
| `polyfill.php` | PHP compatibility polyfills |
| `psr7.php` | `response()`, `request()` |
| `system.php` | `is_cli()`, `base_path()`, `storage_path()` |
| `url.php` | `url()`, `asset()`, `route()` |
| `view.php` | `view()`, `render()` |
| `wp_dummies.php` | WordPress compatibility dummies |

## Most Used Helpers

### Debug
```php
dd($var);       // dump + die
dump($var);     // dump only
```

### Auth & ACL
```php
auth()::getCurrentUserId();
auth()::isAuthenticated();
auth()::getCurrentUser();
acl()::can($userId, $perm);
```

### Database
```php
table('users')->where('active', 1)->get();
db()->select('SELECT NOW()');
withDefaultConnection(fn() => table('x')->get(), 'zippy');
```

### Config & Environment
```php
config('app.name');
env('JWT_SECRET');
config_path('database.main.host');
```

### URLs & Routing
```php
url('/api/v1/users');
asset('css/app.css');
route('products.index');
```

### Internationalization
```php
__('Welcome');
__('Hello %s!', $name);
_e('Hello');
```

### Response Shortcuts
```php
response()->send($data);
response($data);              // alias
error('Not found', 404);
```

### System
```php
is_cli();
base_path();
storage_path('cache/');
```

### Views
```php
view('products.index', ['products' => $list]);
render('partials.header');
```

## Laravel Differences

| Helper | SimpleRest | Laravel |
|--------|-----------|---------|
| `url()` | Generates full URL | Same |
| `response()` | Singleton instance | Factory |
| `dd()` | Same | Same (but SimpleRest adds formatting) |
| `config()` | Same syntax | Same |
| `env()` | Same | Same |
| `view()` | Layout-based | Blade-based |

## See Also

- [`docs/Helpers.md`](../docs/Helpers.md) — full reference
- Source: `src/framework/Helpers/` — all 21 files
