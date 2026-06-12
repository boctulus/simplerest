---
name: routing
description: Complete guide for defining HTTP and CLI routes in SimpleRest using WebRouter, CliRouter, FrontController handlers, and package/module routing.
---

# Routing Skill

SimpleRest has two routing systems: **WebRouter** (HTTP) and **CliRouter** (console), plus a **FrontController** with 6-handler pipeline.

## WebRouter (HTTP)

Routes are defined in `routes/web.php`:

```php
use Boctulus\Simplerest\Core\Router;

Router::get('/products', 'ProductController@index');
Router::post('/products', 'ProductController@store');
Router::get('/products/{id}', 'ProductController@show');
Router::put('/products/{id}', 'ProductController@update');
Router::delete('/products/{id}', 'ProductController@destroy');

// Anonymous functions
Router::get('/health', function() {
    return ['status' => 'ok'];
});
```

### Route Groups

```php
Router::prefix('/api/v1', function() {
    Router::get('/products', 'ProductController@index');
    Router::post('/products', 'ProductController@store');

    Router::prefix('/admin', function() {
        Router::get('/users', 'AdminController@users');
    });
});
```

### Supported HTTP Methods

`GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`

## CliRouter (Console)

Commands are defined in `routes/cli.php`:

```php
use Boctulus\Simplerest\Core\CliRouter;

// Simple command
CliRouter::command('dumb add', 'DumbController@add');

// Multi-word command
CliRouter::command('user create', 'UserController@create');
CliRouter::command('user delete', 'UserController@delete');

// Anonymous function
CliRouter::command('hello', function($name) {
    echo "Hello $name!";
});
```

### Command Groups (via __call)

```php
CliRouter::group('acl', function() {
    CliRouter::command('assign-role', ...);
    CliRouter::command('remove-role', ...);
});
```

Usage: `php com acl assign-role --email=...`

## FrontController Handlers

The 6-handler pipeline in `config/handlers.php`:

1. **PreHandler** — runs before controller
2. **InputHandler** — transforms input
3. **ControllerHandler** — executes controller
4. **OutputHandler** — transforms output
5. **PostHandler** — runs after output
6. **ErrorHandler** — catches exceptions

```php
// Create a custom handler
class MyHandler extends \Boctulus\Simplerest\Core\Handlers\Handler
{
    public function handle($next) {
        // pre-processing
        $result = $next();
        // post-processing
        return $result;
    }
}
```

## Package Routing

Packages define their own routes via `ServiceProvider`:

```php
// In PackageServiceProvider
public function boot(): void
{
    $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    $this->loadCliRoutesFrom(__DIR__ . '/routes/cli.php');
}
```

## Module Routing

Modules use `ModuleProvider` with same pattern:

```php
// ModuleProvider registers web + cli routes
$this->loadRoutesFrom(__DIR__ . '/routes/web.php');
```

## Common Pitfalls

| Problem | Solution |
|---------|----------|
| 404 route | Check `routes/web.php` file exists and is loaded |
| CLI command not found | Check `routes/cli.php` and run `composer dump-autoload` |
| Route conflict | More specific routes first; WebRouter auto-sorts by specificity |
| Package route not working | Verify package is registered in `config/packages.php` |
| Handler not loading | Register in `config/handlers.php` with FQCN |

## Best Practices

- Group related routes under prefixes
- Use controllers, not anonymous functions, for complex logic
- Keep `routes/web.php` and `routes/cli.php` focused on routing only
- Register all package routes via ServiceProvider `boot()`
- Use `php com route:list` to inspect registered routes

## See Also

- [`docs/Routing.md`](../docs/Routing.md) — full 1669-line reference
- [`docs/WebRouter.md`](../docs/WebRouter.md) — quick reference
- [`docs/FrontController.md`](../docs/FrontController.md) — 6-handler pipeline
- `package-creator` skill — routes in packages
- `create-api-endpoint-guide` skill — standard endpoint creation
