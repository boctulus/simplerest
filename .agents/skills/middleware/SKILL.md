---
name: middleware
description: Guides for creating, registering, and using middleware in SimpleRest to intercept and modify controller responses.
---

# Middleware Skill

Middleware intercepts controller responses and can modify them without touching controller code. Used for security checks, response transformation, logging, etc.

## Architecture

Middleware runs **after** the controller method executes and **before** the response is sent. It operates on the already-generated output.

## Creating Middleware

Create a class extending `Middleware` in `src/framework/middlewares/`:

```php
<?php

namespace Boctulus\Simplerest\Middlewares;

use Boctulus\Simplerest\Core\Middleware;

class InyectarSaludo extends Middleware
{
    function handle(){
        $data = response()->get();

        if (is_string($data)){
            // modify response data
            response()->set(str_replace('Hello', 'Hello happy', $data));
        }
    }
}
```

## Registering Middleware

Edit `config/middlewares.php`:

```php
<?php

return [
    // Single controller method
    'Boctulus\Simplerest\Controllers\TestController@mid' => InyectarSaludo::class,

    // All methods of a controller
    'Boctulus\Simplerest\Controllers\FiltersController@__all__' => RestrictContentRoleBased::class,

    // Multiple middlewares for same target
    'boctulus\relmotor_central\controllers\WooCommerceFiltersController@__all__' => [
        RestrictContentRoleBased::class,
        ProcessShortcodesInDescription::class,
    ],
];
```

## Use Cases

### Security / Access Control
```php
class RestrictContentRoleBased extends Middleware
{
    function handle(){
        $data = response()->get();
        $user = auth()::getCurrentUser();

        if (!$user || $user['role'] !== 'admin') {
            // filter sensitive data
            unset($data['sensitive_field']);
            response()->set($data);
        }
    }
}
```

### Response Transformation
```php
class FormatResponse extends Middleware
{
    function handle(){
        $data = response()->get();
        // Wrap in standard envelope
        response()->set([
            'success' => true,
            'data' => $data,
            'timestamp' => time()
        ]);
    }
}
```

### Logging / Audit
```php
class RequestLogger extends Middleware
{
    function handle(){
        $req = Request::getInstance();
        logger()->info('Request: ' . $req->method() . ' ' . $req->getUri());
    }
}
```

## Handler Pipeline (Alternative)

For more complex interceptors, use the 6-handler FrontController pipeline in `config/handlers.php`. Middleware is simpler for response-only transformations.

## Key Points

- Middleware only sees the **response** (not the request)
- Use `response()->get()` to read current response data
- Use `response()->set()` to modify it
- Multiple middlewares run in array order
- Middleware is **controller-specific** (not global)
- For global pre/post processing, use Handlers instead

## See Also

- [`docs/Middlewares.md`](../docs/Middlewares.md) — full middleware reference
- [`docs/FrontController.md`](../docs/FrontController.md) — 6-handler pipeline
- `security-hardening` skill — security middleware patterns
- `routing` skill — FrontController handlers
