---
title: Ciclo de request
source:
  - docs/core/README.md
  - docs/core/request-lifecycle.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

# Ciclo de request

El siguiente recorrido está trazado en el código del checkout. No cubre por completo las APIs de `Request`, `Response`, middleware ni la definición de rutas.

## Entrada y elección del router

`index.php` carga `app.php` y evalúa los componentes en orden. En HTTP, `WebRouter` compila y resuelve las rutas primero. Una coincidencia que se despacha termina esa rama; ante un miss, el control continúa. En CLI, `WebRouter` retorna y `CliRouter` recibe la oportunidad siguiente. Fuera de CLI, `CliRouter` retorna, por lo que una petición HTTP sin ruta atendida puede llegar al `FrontController`.

La compilación ordena rutas por presencia de wildcard, segmentos literales, cantidad total de segmentos y parámetros, con desempates adicionales en el código. Las pruebas inspeccionadas sólo fijan un caso de ruta estática antes de una parametrizada; no prueban todas las combinaciones.

## Resolución del front controller

`FrontController::resolve()` construye handlers según `front_behaviors` y pide al handler de request que analice la URI o los argumentos CLI. Si no obtiene parámetros, retorna. En caso contrario escoge una rama de autenticación, API o controlador ordinario; guarda argumentos de ruta y valida la clase y el método destino. Para una acción API que no sea de autenticación también comprueba la lista de métodos invocables antes de despachar.

Después de la llamada al controlador, el formato de salida corre sólo si el resultado no es `null`; middleware corre después de la acción; una respuesta no vacía se envía y finaliza la rama. Los `Throwable` capturados se delegan al handler de errores configurado. Son pasos condicionales, no una secuencia que atraviese cada request.

`RequestHandler::parse()` lee `REQUEST_URI` para HTTP o `$argv` para CLI. En HTTP quita el segmento `index.php`, aplica `base_url` y separa el path; las banderas de autenticación y API dependen de `remove_api_slug` y los segmentos resultantes.

## Límite de respuesta sin verificar

En una rama de alias exacto de `WebRouter::resolve()`, el código asigna datos de respuesta y sale mientras la llamada contigua a `Response::flush()` está comentada. No se ha reproducido el resultado visible para el usuario; no se debe confiar en esa rama como una respuesta comprobada.

## Alcance de verificación

Los archivos de pruebas consultados cubren registro y compilación de rutas, un caso de prioridad estática y parametrizada, y comportamiento de clones de request/response. Se inspeccionaron, pero no se ejecutaron en esta revisión. La configuración PHPUnit no incluye `unit-tests/`, y ninguna prueba citada establece por sí sola el orden completo del resolver de `index.php`, todos los exits o la rama de alias exacto.
