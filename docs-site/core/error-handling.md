---
title: Manejo de errores y excepciones
source:
  - docs/core/error-handling.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Manejo de errores y excepciones

Hay dos rutas distintas: `FrontController` captura un `Throwable` y lo entrega a un handler configurado; una excepción no capturada puede llegar al handler global de PHP activado por `error_handling`. Sus códigos HTTP y la información expuesta no son iguales.

## Handler de FrontController

El `ErrorHandler` predeterminado registra el mensaje y su archivo/línea, limpia los buffers de salida y responde JSON en CLI o si `Request::acceptsJson()` devuelve true. En los demás casos renderiza `error.php` y termina. Usa como HTTP status el código entero de la excepción cuando está entre 100 y 599; cualquier otro valor pasa a 500.

La rama JSON pasa `$e->getMessage()` a `Response::error()` y la vista HTML también recibe ese mensaje. Este handler no lo oculta según `debug` y no consulta `BaseException::getHttpStatus()` para elegir el status.

## Handler global de excepciones no capturadas

Si `error_handling` está activo, los constructores de `Controller`, `Model` y `View` pueden registrar `set_exception_handler()` con el trait `ExceptionHandler`; la rama de `Model` depende también de `load_config`. Esto registra excepciones no capturadas, pero no un handler PHP para warnings o notices. La última llamada establece el handler global vigente.

El trait intenta rollback y cierre de conexiones de base de datos. Registra mediante `log_error()` cuando `log_errors` está activo, y solo registra un trace cuando también se permiten `debug` y `log_stack_trace`. En CLI imprime diagnóstico y termina con código 1. Para HTTP responde JSON si `remove_api_slug` está activo, la ruta empieza con el segmento API o `Accept` contiene `application/json`; `debug` decide si muestra el mensaje y el trace reales. Su HTTP status es 500.

El `ErrorHandler` de `FrontController` y el handler global son distintos. Un `Throwable` capturado por `FrontController` no llega por esa misma excepción al handler global. `TemporaryExceptionHandler` reutiliza el trait, pero no se registra en el bootstrap consultado.

## Familias de excepciones

`BaseException` añade código de error, argumentos, metadatos de status y `toArray()`. Algunos derivados son `InvalidRequestBodyException` y `MiddlewareNotFoundException`. Otras excepciones del framework heredan de `Exception` o `InvalidArgumentException`; `SqlException` también conserva query/datos y registra el error desde su constructor. Que una clase exponga metadatos no implica que todos los handlers los serialicen.

## Verificación y límites

Ambas rutas se trazaron en fuente, pero no se lanzó ninguna excepción para reproducirlas. Los gates de logging y las diferencias entre handlers se describen en sus referencias separadas.
