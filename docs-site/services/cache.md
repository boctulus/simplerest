---
title: Cache, transitorios y memoización
source:
  - docs/services/cache.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# Cache, transitorios y memoización

Esta referencia describe el código disponible en este checkout. No establece almacenamiento distribuido ni una operación respaldada por una base de datos configurada.

## API y helpers

`ICache` define operaciones estáticas `put`, `get`, `forget` y `expired`; `Cache` declara `NEVER = -1` y `EXPIRED = 0`. No implementa PSR-6. El driver predeterminado de configuración es `DBCache`.

```php
set_transient('report:daily', $report, 300);
$report = get_transient('report:daily', null);
delete_transient('report:daily');
```

`set_transient()` usa un TTL predeterminado de un año; `get_transient()` devuelve `false` ante un miss si no se indica otro valor por defecto. `set_cache_driver()` cambia la configuración sin validar la clase. No hay helper global `cache()` ni método `cached()` en el Query Builder revisado.

## Drivers y expiración

| Driver | Comportamiento observado |
| --- | --- |
| `FileCache` | Serializa archivos con clave SHA-1 bajo `Files::tempDir()`. Su `put()` directo sin TTL usa `-1`, que queda vencido inmediatamente. |
| `DBCache` | Serializa valores en la tabla `cache`. Requiere conexión y tabla; la migración que crea esa tabla está en el repositorio, pero no se ejecutó. Su `put()` directo también vence con TTL `-1`. |
| `InMemoryCache` | Usa un mapa estático, ignora el TTL, no persiste entre procesos y devuelve `null` en un miss. |

`Cache::NEVER` no equivale a “sin vencimiento” en `FileCache` o `DBCache`; `InMemoryCache` tampoco aplica vencimiento. Comprueba el driver que usas. `FileCache` y `DBCache` llaman `unserialize()` sin restringir las clases permitidas: conserva como confiables los archivos y filas que leen.

## Memoización y límites

`Memoization::memoize($key, $callback, $expiration_time)` aplica MD5 a la clave y llama al callback ante un miss. No distingue un miss de un valor guardado como `null`; los drivers tampoco comparten semántica para el TTL predeterminado `null`. Especifica un TTL si dependes de su expiración.

La caché de respuestas salientes tiene una API propia en [ApiClient](/core/api-client). `php com system clear` limpia la caché de Composer y regenera el autoload; no vacía la caché de la aplicación.

Las clases, helpers, configuración y migración se inspeccionaron en fuente. No se ejecutó ninguna operación de caché o base de datos, ni se encontró una suite focalizada de caché bajo `unit-tests/`.
