---
title: Handlers de FrontController
source:
  - docs/core/handlers.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Handlers de FrontController

`config/config.php` define el mapa `front_behaviors` que consume `FrontController::resolve()`. Sus clases se pueden reemplazar, pero no comparten una interfaz ni se ejecutan todas en cada recorrido.

## Métodos consumidos

| Clave | Llamadas de `FrontController` | Cuándo se usa |
| --- | --- | --- |
| `request` | `parse($env)`, `resolveController($params)` | Analiza el entorno; resuelve la rama de controlador ordinario |
| `api` | `resolve($params)` | Resuelve la rama API |
| `auth` | `resolve($params)` | Resuelve la rama de autenticación |
| `output` | `format($controller, $data)` | Solo cuando la acción devuelve datos distintos de `null` |
| `middleware` | `run($class, $method)` | Después de la acción del controlador |
| `error` | `handle($throwable)` | Cuando un `Throwable` llega al `catch` de `FrontController` |

Al comenzar `resolve()`, se instancia cada clase presente en el mapa, antes de elegir una rama. No se comprueba una interfaz común ni se validan los métodos por adelantado. La configuración debe conservar las claves que usa el camino activo; no se rellenan claves ausentes con defaults.

Para reemplazar un handler, cambia su clase bajo la misma clave y conserva las demás entradas requeridas. La clase debe poder construirse sin argumentos y ofrecer los métodos que consume esa clave. No se ejecutó un reemplazo.

## Límites

Estos hooks pertenecen a `FrontController`. Una ruta atendida por `WebRouter` no pasa por este mapa. El handler `error` cubre los throwables capturados dentro de `FrontController`; la ruta global de excepciones no capturadas se explica en [Manejo de errores](error-handling.md).

Las seis claves predeterminadas y sus llamadas se trazaron en la fuente. No se envió una request a `FrontController`.
