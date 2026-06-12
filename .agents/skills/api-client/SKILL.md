---
name: api-client
description: Guide for using ApiClient — SimpleRest's built-in HTTP client (abstraction over cURL) for consuming external APIs. No direct curl usage.
---

# ApiClient Skill

ApiClient is the **mandated** HTTP client for consuming external APIs. No direct `curl_*` functions.

## Basic Usage

```php
use Boctulus\Simplerest\Libs\ApiClient;

$client = new ApiClient('https://api.example.com/products');

$res = $client
    ->get()
    ->getResponse(false);
```

## HTTP Methods

```php
$client = new ApiClient();

// GET
$client->get($url);

// POST
$client->setBody($body)->post($url);

// PUT / PATCH / DELETE
$client->setBody($body)->put($url);
$client->patch($url);
$client->delete($url);

// Custom verb via request()
$client->request($url, 'OPTIONS');
```

## Headers

```php
$client
    ->setHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $token,
    ])
    ->get($url);
```

## Request Body & Data

```php
// JSON body (auto-serialized)
$client->setBody(['name' => 'Product', 'price' => 99.99])->post($url);

// Form data
$client->setData(['username' => 'admin', 'password' => 'secret'])->post($url);
```

## Response Handling

```php
$client->get($url);

$status  = $client->getStatus();    // HTTP status code
$headers = $client->getHeaders();   // response headers
$body    = $client->getResponse(false);  // decoded response
$errors  = $client->getErrors();    // errors if any

// Get raw response
$raw = $client->getResponse(true);
```

## Authentication

```php
// Bearer token
$client->setHeaders(['Authorization' => 'Bearer ' . $token]);

// Basic auth
$client->setAuth($username, $password);
```

## SSL & Redirects

```php
// Disable SSL verification (dev only)
$client->disableSSL();

// Follow redirects
$client->followLocations();
```

## Common Patterns

### Consuming JSON API
```php
$client = (new ApiClient('https://api.example.com'))
    ->setHeaders(['Content-Type' => 'application/json'])
    ->setBody($payload)
    ->post('/v1/orders');

if ($client->getStatus() === 201) {
    $order = $client->getResponse(false);
}
```

### Auth Token Flow
```php
// Login
$login = (new ApiClient())
    ->setBody(['email' => $email, 'password' => $pass])
    ->post('/auth/login');

$token = $login['access_token'] ?? null;

// Use token
$client = (new ApiClient())
    ->setHeaders(['Authorization' => "Bearer $token"])
    ->get('/api/v1/products');
```

## See Also

- [`docs/ApiClient.md`](../docs/ApiClient.md) — full 1038-line reference
- `auth-consumption` skill — consuming auth endpoints
- `security-hardening` skill — secure API communication
