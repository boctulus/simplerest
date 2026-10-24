---
title: EventBus y observadores
source:
  - docs/services/event-bus.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# EventBus y observadores

`EventBus` es un despachador síncrono basado en `EventBusTrait`. La API visible en el código es `attach()`, `detach()`, `notify()`, `setCacheMechanism()` y `getLastEvent()`.

## Registrar y notificar

El observer debe implementar `update($data)`. La interfaz `IObserver` está vacía y no declara el método, aunque `notify()` lo invoca.

```php
use Boctulus\Simplerest\Core\Interfaces\IObserver;
use Boctulus\Simplerest\Core\Libs\EventBus;

$observer = new class implements IObserver {
    public function update(mixed $data): void
    {
        // Reaccionar al payload recibido.
    }
};

$bus = new EventBus();
$bus->attach($observer);
$bus->notify(['type' => 'order.created', 'order_id' => 42]);
$bus->detach($observer);
```

Los observers se indexan por identidad de objeto; adjuntar la misma instancia dos veces no duplica la entrada. `notify($data)` pasa sólo ese argumento a cada observer, en orden. Si uno lanza una excepción, el recorrido se interrumpe y el bus no la captura.

## Último evento y límites

`notify()` también persiste el payload con el driver configurado. El predeterminado es `DBCache`; usa la clave fija `eventbus_last_event` con TTL de siete días. Buses distintos comparten esa clave; no hay canales nombrados ni historial.

```php
use Boctulus\Simplerest\Core\Libs\InMemoryCache;
EventBus::setCacheMechanism(InMemoryCache::class);
```

El driver debe implementar `ICache`. El driver predeterminado depende de que la tabla `cache` exista y sea utilizable; no se comprobó una conexión para esta guía.

El payload no se acompaña de un nombre de evento separado: no hay una API `subscribe()`/`publish()` en esta clase. Los [hooks de Model](/database/model) son extensiones independientes y no se emiten automáticamente a través del EventBus. Se inspeccionó el código, sin ejecutar el controller de demostración; no se encontró una suite focalizada en `unit-tests/`.
