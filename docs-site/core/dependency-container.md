---
title: Container de dependencias
source:
  - docs/core/dependency-container.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Container de dependencias

`Boctulus\Simplerest\Core\Container` es un registro estático de enlaces con construcción explícita. No resuelve recursivamente dependencias de constructor.

## Enlaces y construcción

- `bind($key, $value, $singleton = false)` guarda un nombre de clase, objeto o closure bajo una clave.
- `singleton($key, $value)` crea un enlace con la marca singleton.
- `make($key, $params = [])` resuelve interfaces mediante el mapa de contratos y luego exige un enlace para la clave resultante.
- `makeWith($key, $params)` delega en `make()`.
- `useContract($class, $interface, $implementation)` guarda una relación interfaz-implementación bajo una clave de clase.

Al construir un nombre de clase, los valores se asocian con los nombres de parámetros del constructor. Faltantes requeridos lanzan; parámetros opcionales no enviados reciben `null` en vez de usar el default de PHP. Una closure se invoca sin pasarle `$params`. Para un enlace a nombre de clase marcado singleton, la primera instancia reemplaza el nombre guardado. La rama closure retorna antes de esa caché, por lo que se vuelve a invocar en llamadas posteriores.

```php
use Boctulus\Simplerest\Core\Container;

Container::bind(App\Services\SystemClock::class, App\Services\SystemClock::class);
$clock = Container::make(App\Services\SystemClock::class);
```

El ejemplo muestra el camino de enlace explícito por clase; no se ejecutó. Para resolver una interfaz hacen falta además un mapeo `useContract()` y un enlace para la implementación.

## Límite del lookup de contratos

`getImplementation($interface)` recorre los mapas y devuelve la primera implementación que encuentra. No usa `$class` para elegir una implementación según la clase consumidora. No se debe depender de resolución por consumidor ni de inyección automática hasta corregir y verificar ese camino.

## Verificación y límites

La descripción se trazó en `Container.php` y `Reflector`; no se creó ningún enlace ni objeto con el container.
