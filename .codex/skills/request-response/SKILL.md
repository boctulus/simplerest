---
name: request-response
description: Guides for using the PSR-7-inspired Request (Singleton) and Response (Singleton + immutable) classes in SimpleRest controllers.
---

# Request & Response Skill

Both classes implement **Singleton**. `Response` also supports **immutable PSR-7 methods** (`withStatus`, `withHeader`).

## Request

```php
$req = Request::getInstance();
```

### Query String / CLI Options

```php
$limit  = $req->get('limit', 10);     // ?limit=10 or --limit=10
$hasSku = $req->has('sku');            // boolean
$token  = $req->shiftQuery('token');   // get + remove
```

### Body / Payload

```php
$data     = $req->getBody();             // object (default)
$data     = $req->as_array()->getBody(); // array
$data     = $req->getBody(false);        // force array
$username = $req->getBodyParam('username');
$id       = $req->shiftBodyParam('id'); // get + remove
$decoded  = $req->getBodyDecoded();     // auto JSON or form
```

### Universal Parameter Lookup

`getOption()` / `input()` — searches in order: query string → body → route params → headers

```php
$name  = $req->getOption('name');    // from anywhere
$email = $req->input('email');       // alias
```

### Routing & Method

```php
$method = $req->method();       // GET, POST, etc.
$uri    = $req->getUri();
$header = $req->getHeader('Content-Type');
$token  = $req->bearerToken();
```

### Static Utils

```php
Request::getHeaders();     // all headers
Request::isBrowser();      // check if from browser
```

### CLI-Specific

```php
// php com mycmd run --name=John --age=30
$name = $req->get('name');    // "John"
$age  = $req->get('age');     // "30"
```

## Response

```php
$res = Response::getInstance();
// or via helper:
response()->send($data);
```

### Sending Responses

```php
response()->send($data);                 // 200 default
response()->send($data, 201);            // Created
response()->sendJson($data, 200);        // explicit JSON
response()->sendCode(204);               // No Content
response()->sendOK();                    // 200, no data
```

### Error Responses

```php
response()->error('Not found', 404);
response()->error('Invalid', 400, ['field' => 'email']);
error('Product not found', 404);          // helper alias
```

### Formatting (Return Pattern — Recommended)

```php
return Response::format($data, 200);
return Response::format($user, 201);
return Response::format($products, 200, '', [
    'paginator' => ['total' => 100, 'page' => 1]
]);
```

### Headers

```php
response()
    ->addHeader('X-Custom: value')
    ->addHeaders(['X-One: a', 'X-Two: b'])
    ->send($data);
```

### Pagination

```php
response()
    ->setPaginatorParams($total, count($data), $page, $pages, $pageSize, $nextUrl)
    ->send($data);
```

### Immutable PSR-7 Methods

```php
$new = response()->withStatus(201, 'Created');
$new = response()->withHeader('X-Custom', 'value');
$new = response()->withBody(['data' => 'test']);
$new = response()->withJson(['id' => 1], 201);
```

### Conditional & Utilities

```php
response()->when($isDev, fn($r) => $r->setPretty(true))->send($data);
response()->set($data)->flush();          // defer send
$current = response()->get();             // get current data
```

### Testing

```php
Response::setInstance($mockResponse);
// ... run test ...
Response::setInstance(null);  // restore
```

## Best Practices

### ✅ Do
```php
// Return from controller (FrontController structures it)
return $data;

// Use format() for full control
return Response::format($data, 200);

// Use error() helper for errors
error('Not found', 404);
```

### ❌ Avoid
```php
// Don't return Response instance (causes loop)
return Response::getInstance();  // NO

// Don't send() in reusable methods
response()->send($data);  // NO
```

## See Also

- [`docs/Request.md`](../docs/Request.md) — 929-line reference
- [`docs/Response.md`](../docs/Response.md) — 746-line reference
- [`docs/ImmutableMethods.md`](../docs/ImmutableMethods.md) — PSR-7 with* methods
