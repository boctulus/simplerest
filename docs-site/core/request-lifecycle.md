---
title: Ciclo de request
source:
  - docs/core/README.md
  - docs/core/request-lifecycle.md
source_revision: e11c6fbc630a9af064f50c6f5f1575bf5c45ecc4
status: partial
---

# Recorrido de una petición HTTP

Este recorrido HTTP está trazado en el código del checkout. La página se limita a la entrada HTTP, sus resolvers y las ramas del front controller. No cubre por completo las APIs de `Request`, `Response`, middleware ni la definición de rutas.

## Enrutamiento HTTP y continuidad al front controller

`index.php` carga `app.php`. Si `web_router` está habilitado, incluye `config/routes.php`, compila las rutas y trata de despachar la petición HTTP. Una ruta despachada termina esa rama. Ante un miss, la petición puede continuar al `FrontController` si está habilitado; el orden global de resolvers está en [Arquitectura](/architecture).

Si `web_router` está deshabilitado para un package, la rama de alias exacto retorna al caller; la búsqueda normal sigue con las demás rutas. Un miss de `WebRouter` también retorna.

La compilación ordena rutas por presencia de wildcard, segmentos literales, cantidad total de segmentos y parámetros, con desempates adicionales en el código. Las pruebas inspeccionadas sólo fijan un caso de ruta estática antes de una parametrizada; no prueban todas las combinaciones.

## Resolución del front controller

Cuando una petición HTTP llega a `FrontController::resolve()`, construye handlers según `front_behaviors` y pide al handler de request que analice la URI. Si no obtiene parámetros, retorna. En caso contrario escoge una rama de autenticación, API o controlador ordinario; guarda argumentos de ruta y valida la clase y el método destino. Para una acción API que no sea de autenticación también comprueba la lista de métodos invocables antes de despachar.

Después de la llamada al controlador, el formato de salida corre sólo si el resultado no es `null`; middleware corre después de la acción; una respuesta no vacía se envía y finaliza la rama. Los `Throwable` capturados se delegan al handler de errores configurado. Son pasos condicionales, no una secuencia que atraviese cada request.

Para HTTP, `RequestHandler::parse()` lee `REQUEST_URI`, quita el segmento `index.php`, aplica `base_url` y separa el path; las banderas de autenticación y API dependen de `remove_api_slug` y de los segmentos resultantes.

## Límite de respuesta sin verificar

En una rama de alias exacto de `WebRouter::resolve()`, el código asigna datos de respuesta y sale mientras la llamada contigua a `Response::flush()` está comentada. No se ha reproducido el resultado visible para el usuario; no se debe confiar en esa rama como una respuesta comprobada.

## Alcance de verificación

Los archivos de pruebas consultados cubren registro y compilación de rutas, un caso de prioridad estática y parametrizada, y comportamiento de clones de request/response. Se inspeccionaron, pero no se ejecutaron en esta revisión. `composer test` y la suite declarada en `phpunit.xml` incluyen `unit-tests/`; esto confirma la selección, no una ejecución exitosa. Ninguna prueba citada establece por sí sola el traspaso de una ruta HTTP al front controller, todos los exits o la rama de alias exacto.
