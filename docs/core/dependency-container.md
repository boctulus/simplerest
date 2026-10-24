# Dependency container

`Boctulus\Simplerest\Core\Container` is a static binding registry with explicit construction methods. It does not recursively resolve constructor dependencies.

## Bindings and construction

- `bind($key, $value, $singleton = false)` stores a class name, object, or closure under a key.
- `singleton($key, $value)` is shorthand for a binding with the singleton flag.
- `make($key, $params = [])` resolves an interface through the registered contract map, then requires a binding for the resulting key.
- `makeWith($key, $params)` delegates to `make()`.
- `useContract($class, $interface, $implementation)` stores an interface-to-implementation association under a class key.

When a class-name binding is constructed, supplied values are matched to constructor parameter names. Missing required values throw; unprovided optional values are passed as `null` by the current implementation rather than omitted so PHP can apply their defaults. When the binding is a closure, `make()` invokes it without forwarding `$params`. For a class-name binding marked singleton, the first created object replaces the stored class name. A closure result is returned before that cache branch, so a closure marked singleton is invoked again on later `make()` calls.

```php
use Boctulus\Simplerest\Core\Container;

Container::bind(App\Services\SystemClock::class, App\Services\SystemClock::class);
$clock = Container::make(App\Services\SystemClock::class);
```

This example represents the explicit class-key binding path in source; it was not executed. Interface lookup additionally requires a `useContract()` mapping and a binding for the implementation class.

## Contract lookup limit

`getImplementation($interface)` scans the stored contract maps and returns the first mapping for that interface. It does not use the `$class` key to select a consumer-specific implementation. The resolved implementation must also have its own binding before `make($interface)` can construct it. Do not rely on `useContract()` for per-consumer resolution or automatic injection until that behavior is corrected and verified.

## Evidence and limits

The behavior above is traced to `Container.php` and `Reflector`; no container binding or construction was run. The contract lookup limitation is recorded in the [core runtime audit](../audit/README.md#core-runtime-and-http-audit-2026-10-24).
