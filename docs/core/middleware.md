# Middleware

This reference describes the middleware paths currently present in the checkout. Middleware runs after the selected controller method; it is not a pre-dispatch guard or a global request pipeline.

## Base class and registration

Extend `Boctulus\Simplerest\Core\Middleware` and implement `handle()`. The base constructor initializes protected `$req` and `$res` from the `request()` and `response()` helpers when those properties are `null`. The return value of `handle()` is not consumed by either dispatch path; middleware that changes output must update the shared response.

`config/middlewares.php` returns a map whose key is `ControllerClass` or `ControllerClass@method`. The value is one middleware class or an array of classes. When the method part is omitted, the dispatcher treats it as `index`. The special method `__all__` matches every method for that controller.

```php
return [
    Boctulus\Simplerest\Controllers\TestController::class . '@mid' =>
        Boctulus\Simplerest\Middlewares\InyectarSaludo::class,
];
```

The map above uses the controller and middleware names already present in this checkout; the middleware was not run. A method-specific key is formed with `Controller::class . '@method'`.

## When middleware runs

There are two controller dispatch paths:

- `FrontController` calls the controller method, formats non-`null` output, then calls the configured middleware handler before flushing a non-empty response.
- The controller branch in `WebRouter` calls the method, stores its returned data (or the existing response when the method returns `null`), then runs its own middleware loop before flushing.

`WebRouter` closure handlers use a separate branch and skip the configured middleware loop. This difference is recorded in the [routing audit](../audit/README.md#claim-level-findings-routing-apis). Multiple middleware classes run in configuration order and, for an array value, array order. Each is constructed with `new $class()` and then `handle()` is called.

Because dispatch occurs after the controller action, this middleware cannot stop that action from running. It can inspect the request and current response through its inherited properties or the global helpers, and it can mutate the response before the normal flush. There is no `next()` callback in this contract.

## Evidence and limits

The behavior is traced to `Middleware`, `MiddlewareHandler`, `WebRouter`, `FrontController`, `config/middlewares.php`, and the application autoload map. The legacy middleware skill and older routing pages contain broader claims; the public contract here follows the current source. No middleware example or route was executed.
