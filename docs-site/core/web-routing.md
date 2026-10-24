---
title: Routing HTTP con WebRouter
source:
  - docs/core/web-routing.md
  - docs/audit/README.md
source_revision: cfb6365c0bea2fcfc90b807f0f6a5c61740d6753
status: partial
---

# Routing HTTP con WebRouter

`WebRouter` registra y despacha rutas HTTP. Esta guía no describe `CliRouter`: el registro global de resolvers está en [Arquitectura](/architecture), el ciclo HTTP en [Ciclo de request](/core/request-lifecycle) y la CLI tiene su [guía de routing propia](/cli/routing).

## Registro de rutas

La aplicación define rutas en `config/routes.php`, que `index.php` incluye cuando `web_router` está activo. `WebRouter::compile()` construye patrones y resuelve de antemano las clases y métodos de controladores; `resolve()` atiende HTTP y retorna al caller si no encuentra una ruta. Los ServiceProvider de paquetes pueden cargar sus propios archivos `config/routes.php`.

Los métodos de registro disponibles son `get()`, `post()`, `put()`, `patch()`, `delete()` y `options()`. `match($verbs, $uri, $handler)` registra varios verbos; `any()` replica la ruta en GET, POST, PUT, PATCH, DELETE y OPTIONS. Los verbos pasados a `match()` se convierten a mayúsculas.

```php
WebRouter::get('users/{id}', function ($id) {
    return ['id' => $id];
})->where(['id' => '[0-9]+']);

WebRouter::group('api/v1', function () {
    WebRouter::get('health', function () {
        return ['ok' => true];
    });
});
```

El ejemplo ilustra la forma de registro fuente; no se ejecutó esta ruta. `group($prefix, $callback)` antepone segmentos y admite grupos anidados. `where($patterns)` se encadena después de registrar una ruta y asocia expresiones regulares con los nombres entre llaves.

Un handler puede ser una función o closure, o una cadena `Controller@method`; sin `@method`, el compilador usa `index`. Un nombre que contiene `\\` se trata como clase completa; los demás se resuelven en el namespace de controladores de la aplicación. El compilador comprueba clase y método. El `config/routes.php` del checkout contiene rutas de desarrollo y administración; no lo trates como una plantilla predeterminada para copiar a otra aplicación.

## Parámetros, validación y wildcard

`{id}` crea un segmento dinámico que por defecto captura cualquier secuencia sin `/`. `where(['id' => '[0-9]+'])` sustituye la captura por una expresión configurada; para varios parámetros se usa un mapa con cada nombre.

El sufijo `/*` crea una captura opcional del resto del path. En una ruta como `files/*`, el valor capturado se pasa como argumento final. La sintaxis de wildcard identificada es ese sufijo; no se afirma soporte de otros formatos.

`compile()` construye regex para rutas dinámicas y ordena rutas en cada verbo: primero sin wildcard, luego más segmentos literales, más segmentos totales, menos parámetros, mayor longitud y desempate lexicográfico. Los tests fuente cubren el registro de rutas y un caso de ruta estática antes que una parametrizada; no se ejecutaron y no demuestran todos los desempates.

En la rama estática, `resolve()` compara los segmentos definidos con el prefijo del path y pasa los segmentos restantes como argumentos. No asumas que todas las rutas estáticas rechazan sufijos extra; ese contrato no está cubierto por las pruebas inspeccionadas. En la rama de controller, WebRouter ejecuta la lista de `config/middlewares.php`; los handlers closure siguen una rama distinta y no recorren ese bloque de middleware.

## Arrays, mounts y nombres

`fromArray()` acepta un mapa como `['GET:health' => $handler]`; sin prefijo de verbo registra el handler en todos los verbos admitidos. Los verbos admitidos para estas claves son GET, POST, PUT, PATCH, DELETE y OPTIONS. `mount($prefix, $handlerClass)` registra un wildcard para todos esos verbos y delega al método `resolve()` del handler.

`name($name)` registra metadatos para la ruta actual y `getRouteByName($name)` arma una URL usando el protocolo y `SERVER_NAME`. El test inspeccionado solo comprueba que se guardó el nombre. El lookup de alias durante `resolve()` usa el path entrante como clave, mientras el registro usa el nombre; el branch de alias también termina sin llamar a `Response::flush()`. Por eso no se prescribe el dispatch de aliases como contrato comprobado. Este hallazgo se mantiene en el registro de auditoría de la fuente canónica.

## Respuesta y límites de ejecución

Una ruta HTTP coincidente termina el branch de WebRouter. El handler produce datos para `Response`; `flush()` emite y termina el proceso cuando se alcanza la rama normal. En la rama de controller se cargan los middlewares de la aplicación antes de emitir. Un miss retorna al caller para que el siguiente resolver activo pueda continuar; el orden global se describe en [Arquitectura](/architecture).

`WebRouterTest.php` inspecciona verbos, grupos, parámetros, `where()`, `fromArray()`, nombres, patrones y orden. `WebRouterFunctionalTest.php` contiene peticiones a un servidor configurado; ni ese test ni el unitario se ejecutaron en esta auditoría. No se afirma que las rutas del ejemplo ni el servidor local actual respondan.

## Índice de métodos

Los métodos públicos identificados en `WebRouter` son: `getInstance`, `group`, `resolve`, `mount`, `compile`, `where`, `get`, `post`, `put`, `patch`, `delete`, `options`, `any`, `match`, `name`, `getRouteByName`, `fromArray`, `getRoutes`, `getRoutePatterns` y `getRouteParamNames`. Es un índice a nivel de fuente, no una garantía de ejecución de cada rama.
