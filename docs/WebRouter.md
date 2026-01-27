# WebRouter Documentation

## Overview

WebRouter is the routing system for the SimpleRest framework. It handles incoming HTTP requests and maps them to appropriate controllers or callback functions.

## Basic Route Definition

```php
WebRouter::get('uri', 'Controller@method');
WebRouter::post('uri', 'Controller@method');
WebRouter::put('uri', 'Controller@method');
WebRouter::patch('uri', 'Controller@method');
WebRouter::delete('uri', 'Controller@method');
WebRouter::options('uri', 'Controller@method');
WebRouter::any('uri', 'Controller@method');
```

## Advanced Features

### Soporte de "any"

The `any` method registers a route that responds to all HTTP verbs:

```php
WebRouter::any('uri', 'Controller@method');
// This route will respond to GET, POST, PUT, PATCH, DELETE, and OPTIONS requests
```

### Soporte de wildcards

The WebRouter supports wildcard routes with the following characteristics:

* **Tipo catch-all**, correctamente integrado al sistema existente
* **Solo como último segmento** - wildcards must appear at the end of the URI
* **Captura todo el resto del path** - everything after the wildcard prefix is captured
* **Se inyecta como último parámetro del callback** - the captured path becomes the last parameter
* **Convive con {param} y where()** - works alongside regular parameters and constraints
* **No rompe compatibilidad previa** - maintains backward compatibility
* **Mantiene el ordenamiento por especificidad** - preserves route precedence rules

#### Wildcard Examples

```php
// Simple catch-all route
WebRouter::get('files/*', function($path) {
    return [
        'endpoint' => 'files',
        'requested_path' => $path,
        'message' => 'Wildcard route for files endpoint captured the path'
    ];
});

// Parameterized wildcard route
WebRouter::get('user/{id}/*', function($id, $resource) {
    return [
        'user_id' => $id,
        'resource' => $resource,
        'message' => 'User wildcard route accessed'
    ];
});

// Complex API route with multiple parameters and wildcard
WebRouter::get('api/v1/{resource}/{id}/*', function($resource, $id, $action) {
    return [
        'resource_type' => $resource,
        'resource_id' => $id,
        'action' => $action,
        'message' => 'API wildcard route with multiple parameters'
    ];
});
```

### Soporte de match

The `match` method allows registering a route for specific HTTP verbs:

#### Match simple
```php
WebRouter::match(['GET', 'POST'], 'login', 'AuthController@login');
```

#### Con parámetros dinámicos y where
```php
WebRouter::match(['PUT', 'PATCH'], 'users/{id}', 'UserController@update')
    ->where(['id' => '\d+']);
```

#### Dentro de un group
```php
WebRouter::group('api', function () {
    WebRouter::match(['GET', 'OPTIONS'], 'status', function () {
        return ['ok' => true];
    });
});
```

## Route Groups

Groups allow organizing routes with a common prefix:

```php
WebRouter::group('admin', function() {
    WebRouter::get('dashboard', 'AdminController@dashboard');
    WebRouter::get('users', 'AdminController@users');
});
```

## Route Parameters

Dynamic routes with parameters:

```php
WebRouter::get('user/{id}', 'UserController@show');
```

### Parameter Constraints

Use the `where` method to constrain parameter formats:

```php
WebRouter::get('user/{id}', 'UserController@show')
    ->where(['id' => '[0-9]+']);  // Only numeric IDs
```

## Route Resolution

The router resolves routes in order of specificity:
1. Static routes (most specific)
2. Routes with parameters
3. Routes with wildcards (least specific)

This ensures that more specific routes take precedence over more generic ones.

## From Array

Define multiple routes from an array:

```php
WebRouter::fromArray([
    'GET:/users' => 'UserController@index',
    'POST:/users' => 'UserController@store',
    'GET:/posts' => 'PostController@index',
]);
```

## Author

Pablo Bozzolo (boctulus)
Software Architect