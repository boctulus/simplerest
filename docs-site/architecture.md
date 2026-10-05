---
title: Arquitectura
source:
  - docs/architecture.md
  - docs/core/request-lifecycle.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

# Arquitectura

Esta vista resume las relaciones de arranque y despacho trazadas en el checkout actual. No es un catálogo completo de componentes ni prueba que el repositorio pueda instalarse como aplicación consumidora independiente.

## Puntos de entrada

- `index.php` carga `app.php` y después evalúa, en ese orden, `web_router`, `console_router` y `front_controller`.
- `app.php` carga scripts de arranque y redirección, autoloading de Composer y de la aplicación, valores de entorno, configuración, helpers y proveedores configurados.
- `com` es el punto de entrada CLI del repositorio. El descubrimiento y el comportamiento completo de sus comandos siguen pendientes de documentar.

## Despacho HTTP

Con la configuración incluida, los tres componentes están habilitados. Para una petición HTTP:

1. `index.php` incluye `config/routes.php`, compila las rutas y llama a `WebRouter::resolve()`.
2. Si `WebRouter` despacha una ruta coincidente, termina esa rama antes de `CliRouter` y `FrontController`.
3. Si no hay coincidencia, `WebRouter` retorna; `CliRouter::resolve()` también retorna fuera de CLI.
4. Si sigue habilitado, `FrontController::resolve()` recibe la petición.

Al llegar al front controller, se construyen handlers desde `front_behaviors`. La resolución selecciona una rama de autenticación, API o controlador ordinario, comprueba la clase y el método, y para API no-auth verifica que la acción esté en la lista de métodos invocables. Formato de salida, middleware, flush y handler de errores dependen de la rama y del resultado; no todos se ejecutan en cada petición.

## Límites entre framework y aplicación

Composer mapea `Boctulus\Simplerest\Core\` a `src/framework/`; el namespace de aplicación también tiene rutas de autoload para `src/` y `app/`, y `app.php` añade carga de módulos desde `app/Modules/`.

Este checkout reúne el código del framework y una aplicación configurada. La existencia de `src/framework/` no demuestra por sí sola que una instalación consumidora funcione sin los archivos de la aplicación.

Para el detalle de las ramas HTTP y CLI, consulta el [ciclo de request](/core/request-lifecycle).
