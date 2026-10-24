# Cache, transitorios y memoización

Esta referencia describe las implementaciones que existen en el checkout. El contrato es de código, no una promesa de almacenamiento distribuido ni una prueba con una base configurada.

## API disponible

`ICache` define operaciones estáticas `put`, `get`, `forget` y `expired`; `Cache` añade las constantes `NEVER = -1` y `EXPIRED = 0`. No es una implementación de PSR-6. Las clases del framework son `FileCache`, `DBCache` e `InMemoryCache`, pero sus reglas de vencimiento no son equivalentes.

La aplicación configura `cache_driver`; en este checkout el valor predeterminado es `DBCache`. Los helpers globales `set_transient`, `get_transient`, `delete_transient` y `set_cache_driver` resuelven ese driver en cada llamada:

```php
set_transient('report:daily', $report, 300);
$report = get_transient('report:daily', null);
delete_transient('report:daily');
```

El helper `set_transient()` usa un TTL predeterminado de un año y `get_transient()` devuelve `false` cuando no se pasa otro valor por defecto. `set_cache_driver()` cambia la opción de configuración, pero no valida la clase recibida. No hay una función global `cache()` ni un método `cached()` en el Query Builder de este checkout.

## Drivers presentes

| Driver | Almacenamiento y límites observados |
| --- | --- |
| `FileCache` | Guarda archivos serializados con una clave SHA-1 bajo `Files::tempDir()`. `put()` devuelve `true` o `false`; `get()` devuelve el valor o el fallback. El `put()` directo sin TTL usa `-1`, que genera una fecha ya vencida y no se comporta como “sin vencimiento”. |
| `DBCache` | Serializa valores en la tabla `cache` mediante `DB::table('cache')`. Depende de una conexión y de que la tabla exista; el repositorio contiene una migración para crearla, que no se ejecutó. `clear()` elimina las filas de esa tabla. Su `put()` directo también tiene TTL predeterminado `-1`, que vence inmediatamente. |
| `InMemoryCache` | Mantiene un mapa estático en memoria. Ignora el TTL, no conserva datos entre procesos y devuelve `null` ante un miss. |

Aunque `Cache::NEVER` vale `-1`, los `put()` de `FileCache` y `DBCache` no lo interpretan como almacenamiento permanente. `InMemoryCache` tampoco aplica el vencimiento. Comprueba la semántica del driver concreto antes de depender de un TTL.

`FileCache` y `DBCache` usan `serialize()`/`unserialize()` sin una lista de clases permitidas. Guarda solo valores que controles y cuyo contenido puedas tratar como confiable; la caché no valida ni autentica sus archivos o filas.

## Memoización y otras cachés

`Memoization::memoize($key, $callback, $expiration_time)` aplica MD5 a la clave, busca el resultado con el driver configurado y llama al callback ante un miss. No puede distinguir entre un miss y un valor guardado como `null`. El parámetro de vencimiento predeterminado es `null`, y cada driver lo maneja de forma diferente; indica un TTL explícito cuando uses esta clase.

La caché de respuestas salientes pertenece a [`ApiClient`](../core/api-client.md) y usa su propia API. El Query Builder no expone `cached()` en la fuente revisada. El comando `php com system clear` limpia la caché de Composer y regenera el autoload; no vacía `DBCache` ni `FileCache`.

## Límites de verificación

La configuración predeterminada, las tres clases, los helpers, `Memoization` y la migración de la tabla `cache` se inspeccionaron en fuente. No se encontró una suite focalizada de caché en `unit-tests/`, ni se ejecutó una operación de caché o base de datos. La tabla de [conexiones](../database/connections.md) documenta por separado la selección de conexión.
