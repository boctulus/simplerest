---
title: Logging
source:
  - docs/core/logging.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Logging

El framework ofrece `Logger` y los helpers globales `logger()`, `log_error()` y `log_sql()`. Sus gates de configuración difieren; llamar directamente a `Logger` no sigue todos los gates de los helpers.

## Helpers y gates

| Helper | Gate | Destino |
| --- | --- | --- |
| `logger($data, $path, $append)` | `debug` | Log general |
| `log_error($error)` | `log_errors` | Log de errores |
| `log_sql($sql)` | `debug` y `log_sql` | Log SQL |

`Logger::log()`, `Logger::logError()` y `Logger::logSQL()` se pueden llamar directamente. `ErrorHandler` llama directamente a `Logger::logError()` y `Logger::log()`, de modo que `log_errors=false` no suprime todos los logs de error.

## Salida de Logger

La fuente define `log.txt`, `errors.txt` y `sql_log.txt` como filenames iniciales. `Logger::_log()` añade timestamp por default y usa PHP `error_log()` si la clave de configuración `error_log` es true; si es false, llama a `Files::writeOrFail()`.

Dos overrides de filename no funcionan como su nombre sugiere en este checkout: `getLogFilename()` inicia con `log.txt` no nulo, por lo que no consulta `log_file` en el camino por default; `setSqlfilename()` asigna el filename de errores aunque `logSQL()` escribe el filename SQL.

El trait `ErrorReporting` recopila mensajes estructurados por severidad; no registra callbacks globales de errores o excepciones de PHP.

## Verificación y límites

`LoggerErrorConfigurationTest.php` contiene aserciones para `log_error()` con `log_errors` activo e inactivo. Se inspeccionaron, no se ejecutaron. No se llamó a un helper de logging ni se escribió un archivo durante la auditoría.
