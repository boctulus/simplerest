---
title: Middleware
source:
  - docs/core/middleware.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Middleware

La implementación actual ejecuta middleware después del método del controlador. No es un guard previo al despacho ni un pipeline global de requests.

## Clase base y registro

Extiende `Boctulus\Simplerest\Core\Middleware` e implementa `handle()`. El constructor base obtiene `request()` y `response()` cuando `$req` o `$res` son `null`. El dispatcher ignora el valor de retorno de `handle()`; para cambiar la salida hay que modificar la respuesta compartida.

`config/middlewares.php` devuelve un mapa cuya clave es `ControllerClass` o `ControllerClass@method`. El valor puede ser una clase o un array de clases. Si se omite el método, se usa `index`; el método especial `__all__` coincide con todos los métodos del controlador.

```php
return [
    Boctulus\Simplerest\Controllers\TestController::class . '@mid' =>
        Boctulus\Simplerest\Middlewares\InyectarSaludo::class,
];
```

El ejemplo usa nombres presentes en este checkout, pero no se ejecutó.

## Cuándo se ejecuta

Hay dos caminos de controlador:

- `FrontController` llama al método, formatea el resultado solo si no es `null` y después ejecuta el handler de middleware antes de vaciar una respuesta no vacía.
- La rama de controlador de `WebRouter` llama al método, guarda el valor devuelto o la respuesta existente cuando el resultado es `null`, y luego recorre su propio bloque de middleware antes del flush.

Los handlers closure de `WebRouter` usan otra rama y omiten el bloque de middleware configurado. La guía de [Routing HTTP](/core/web-routing) describe el dispatch de `WebRouter`; aquí se limita el alcance al middleware. Varios middlewares se ejecutan en orden de configuración y, para un array, en orden de sus elementos. Cada clase se instancia con `new $class()` y luego recibe la llamada `handle()`.

Como la acción del controlador ya ocurrió, este middleware no puede impedir que se ejecute. Puede leer el request y la respuesta actual mediante sus propiedades heredadas o los helpers globales, y modificar la respuesta antes del flush normal. Este contrato no define un callback `next()`.

## Verificación y límites

La guía describe `Middleware`, `MiddlewareHandler`, `WebRouter`, `FrontController` y `config/middlewares.php` según la fuente. No se ejecutó ninguna ruta ni ejemplo de middleware.
