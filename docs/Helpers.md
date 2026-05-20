# Helpers Globales — SimpleRest

SimpleRest tiene **21 archivos de helpers** en `src/framework/Helpers/` con funciones globales disponibles en toda la aplicación.

---

## Carga Automática

Los helpers se cargan en `app.php` (bootstrap) y están disponibles globalmente sin necesidad de `use` o `require`.

---

## Lista de Helpers

| Archivo | Funciones principales |
|---------|----------------------|
| `cache.php` | `cache()`, `cache_remember()` |
| `config.php` | `config()`, `config_path()` |
| `date.php` | `now()`, `today()`, `format_date()` |
| `db.php` | `db()`, `table()`, `withDefaultConnection()` |
| `debug.php` | `dd()`, `dump()`, `debug()` |
| `env.php` | `env()`, `env_exists()` |
| `etc.php` | Varias utilidades misceláneas |
| `factories.php` | `auth()`, `acl()`, `get_user_model_name()` |
| `gdrive.php` | `gdrive()` |
| `html.php` | `html()`, `script()`, `style()` |
| `http.php` | `redirect()`, `back()`, `cors()` |
| `i18n.php` | `__()`, `_e()`, `trans()` |
| `loggers.php` | `logger()`, `log_error()` |
| `options.php` | `option()`, `update_option()` |
| `package.php` | `register_package()`, `discover_packages()` |
| `polyfill.php` | Polyfills para compatibilidad PHP |
| `psr7.php` | `response()`, `request()` (PSR-7) |
| `system.php` | `is_cli()`, `base_path()`, `storage_path()` |
| `url.php` | `url()`, `asset()`, `route()` |
| `view.php` | `view()`, `render()` |
| `wp_dummies.php` | Dummies para compatibilidad WordPress |

## Helpers Comunes

### Debug
```php
dd($variable);     // dump + die
dump($variable);   // dump sin die
```

### Auth
```php
auth()::getCurrentUserId();
auth()::isAuthenticated();
```

### Rutas
```php
url('/api/v1/users');
asset('css/app.css');
```

### Config
```php
config('app.name');
config('database.main.host');
```

### DB
```php
table('users')->where('active', 1)->get();
withDefaultConnection(function() {
    // operaciones con conexión por defecto modificada
}, 'zippy');
```

### i18n
```php
__('Welcome');
_e('Hello %s!', $name);
```

### Response
```php
response()->withHeader('X-Custom', 'value');
response()->withStatus(201);
```

### Request
```php
request()->getMethod();
request()->input('email');
```

## Ver También

- [`src/framework/Helpers/`](../src/framework/Helpers/) — código fuente
