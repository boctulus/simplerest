# EventBus y observadores

`EventBus` implementa un despachador síncrono basado en `EventBusTrait`. La API registrada en el código consta de `attach()`, `detach()`, `notify()`, `setCacheMechanism()` y `getLastEvent()`.

## Registrar y notificar

El observer debe tener un método `update($data)`. La interfaz `IObserver` está vacía y no declara ese método; `EventBusTrait` lo invoca al notificar.

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

`attach()` indexa observers por identidad de objeto, por lo que adjuntar dos veces la misma instancia no crea dos entradas. `notify($data)` recorre la lista en orden y pasa únicamente `$data` a `update()`. Si un observer lanza una excepción, el recorrido se interrumpe; la implementación no captura errores por observer.

## Persistencia de la última carga

Además de llamar a los observers, `notify()` guarda el payload con la clase de caché configurada. El valor predeterminado es `DBCache`; la escritura usa la clave fija `eventbus_last_event` y TTL de siete días. `EventBus::getLastEvent()` lee esa clave.

```php
use Boctulus\Simplerest\Core\Libs\EventBus;
use Boctulus\Simplerest\Core\Libs\InMemoryCache;

EventBus::setCacheMechanism(InMemoryCache::class);
```

Cambiar el driver afecta la persistencia, no el despacho. Con la clave fija, buses distintos comparten la misma “última carga”; no hay nombre de canal ni historial. `setCacheMechanism()` exige una clase que implemente `ICache`. El driver predeterminado necesita una tabla `cache` utilizable, y no se comprobó una conexión para esta guía.

## Límites del contrato

El método no recibe un nombre de evento separado del payload y el observer recibe un solo argumento. No hay `subscribe()`, `publish()` ni un registro de eventos nombrados en esta clase.

Los hooks `onCreating()`, `onUpdating()` y otros de `Model` son llamadas de extensión propias de los caminos de persistencia, no emisiones de este `EventBus`; véase [Model y ModelRecord](../database/model.md). No se encontró una suite focalizada de `EventBus` bajo `unit-tests/`; la clase y el controller de demostración se inspeccionaron, pero no se ejecutaron.
