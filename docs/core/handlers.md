# FrontController handlers

`config/config.php` provides the `front_behaviors` map used by `FrontController::resolve()`. It is a set of replaceable class names, not a shared interface or a guarantee that every method runs for every request.

## Methods consumed by the controller

| Key | Calls made by `FrontController` | When used |
| --- | --- | --- |
| `request` | `parse($env)`, `resolveController($params)` | Parses the environment and resolves an ordinary controller branch |
| `api` | `resolve($params)` | Resolves an API branch |
| `auth` | `resolve($params)` | Resolves an authentication branch |
| `output` | `format($controller, $data)` | Called only when the controller method returns non-`null` data |
| `middleware` | `run($class, $method)` | Called after the controller action |
| `error` | `handle($throwable)` | Called when a `Throwable` reaches the `FrontController` catch block |

The controller instantiates every class present in the map at the start of `resolve()`, before it knows which branch will be used. It does not check for an interface or validate required methods in advance. The configured map must contain the keys the active path later reads; the implementation does not fill missing entries from defaults.

Replace a class name under its key while retaining the other required entries. A replacement must be constructible without arguments and expose the methods consumed for its key; no handler replacement was executed.

## Boundaries

These hooks belong to `FrontController`. A route dispatched by `WebRouter` does not enter this handler map. The `error` handler also covers only throwables caught inside `FrontController`; the global uncaught-exception path controlled by `error_handling` is described in [Error handling](error-handling.md).

The six default keys and their order of use are source-traced from `config/config.php` and `FrontController.php`. No replacement handler or front-controller request was run.
