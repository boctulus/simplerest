---
title: Resolución y parámetros de endpoints automáticos
source:
  - docs/api/automatic-endpoints.md
  - docs/audit/README.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# Resolución y parámetros de endpoints automáticos

Esta guía sigue el recorrido de los controladores y la conversión de parámetros en fuente. No afirma que se haya ejecutado un CRUD generado correctamente.

## Resolución del recurso

Con `remove_api_slug = false`, `ApiHandler::resolve()` espera después del segmento `api` otro segmento con forma de versión. Para un slug ordinario deriva una clase de aplicación dentro del namespace configurado `Controllers\\api`; `trashcan` y `collections` tienen mapeos especiales a controladores core. El resolver devuelve la clase destino, el método, los argumentos y la versión. No consulta los esquemas de base de datos ni crea clases de controlador.

`FrontController::resolve()` verifica que existan la clase y el método (con la excepción del método mágico `__call`). Para requests API que no sean de autenticación también requiere que el método esté incluido en `getCallable()` antes del despacho.

## Generador y modelo

`make api` escribe un scaffold de controlador a partir de `ApiRestfulController`, que extiende el `ApiController` core. La clase base declara caminos genéricos GET, POST, PUT, PATCH y DELETE. El scaffold fija literalmente el indicador de soft-delete en `true`; no deriva su valor desde el esquema.

Si no se asigna explícitamente un modelo, `ApiController` deriva su nombre desde la clase del controlador y solicita la instancia usando el mapeo de la conexión activa. Un descriptor de esquema no genera por sí solo ese modelo ni el controlador. El resultado todavía depende de autenticación, ACL, carga del esquema, conexión y ruta del request.

La fuente confirma caminos separados para generadores de esquema, modelo, controlador API y generador combinado, además del resolver y el despacho. No demuestra que `make any <table> --schema --model --api` produzca CRUD funcional. No se ejecutó ningún generador ni una petición API.

## Métodos HTTP y autorización

El resolver usa el método HTTP como nombre de método destino. El `ApiController` genérico implementa `get`, `post`, `put`, `patch` y `delete`; `head()` llama a `get()` con buffering de salida y `options()` está vacío. El constructor crea la lista invocable a partir del ACL de tabla o recurso, y el front controller la comprueba antes de despachar un método API que no sea de autenticación. Que un método exista no significa que cualquier llamador pueda invocarlo.

El resolver valida que el path tenga un segmento con forma de versión y lo devuelve junto con el controlador, método y argumentos. Esta ruta no selecciona una implementación de controlador por versión. Los slugs se normalizan de snake_case a PascalCase; `trashcan` y `collections` se asignan a clases API core. Estas ramas se trazaron en fuente, pero no se probaron por HTTP.

## Parámetros GET de colecciones

`ApiController::get()` consume los parámetros siguientes antes de llamar al modelo o Query Builder:

| Parámetro | Comportamiento trazado y límite |
| --- | --- |
| `_schema` | Devuelve temprano los metadatos `get()` de la clase de esquema seleccionada. |
| `defs` | Devuelve definiciones del modelo, incluidas las de tablas relacionadas cuando están disponibles. |
| `fields` | Proyección separada por comas; cada campo debe existir en los atributos del modelo o el método retorna error 400. |
| `exclude` | Lista de campos a ocultar, separados por comas y comprobados contra los atributos del modelo. |
| `pretty` | Activa formato legible salvo los valores false-like que reconoce el código. |
| `page`, `pageSize` | Usa los nombres configurados para el paginador. El tamaño tiene límite `paginator.max_limit`; si se solicita paginación sin página, el default es 1. Consulta también [Paginación](/database/pagination). |
| `limit`, `offset` | Se usa cuando no se enviaron `page`/`pageSize`. Un límite ausente o cero selecciona el máximo configurado; uno positivo queda limitado a ese máximo. |
| `orderBy` | Se pasa al Query Builder como expresión de orden. |
| `order[field]` | Se acepta como mapa si cada dirección es `ASC` o `DESC`, sin distinguir mayúsculas. La rama no implementa la clave anidada `orderBy[field]`. |
| `props` | Reconoce `min`, `max`, `sum`, `avg` o `count` con un campo o `*`. La ruta opcional de alias lee el nombre de la función en la variable de alias; no se prescribe su resultado. |
| `groupBy` | Divide una cadena separada por comas y pasa los campos al builder. |
| `having` | Analiza comparaciones acotadas de función/campo o alias contra un literal numérico. La fuente señala que falta paginación agrupada/con having. |
| `include`, `_related` | Entran en el camino de subrecursos si hay relaciones configuradas. `include` reduce ese conjunto; no se reprodujo el resultado de la relación. |
| `folder` | Selecciona una rama con búsqueda de carpeta y comprobaciones ACL; esta guía no afirma una petición de carpeta exitosa. |

Los parámetros ordinarios de colección pasan a filtros del modelo después de una conversión específica de API. Los operadores de comparación trazados son `eq`, `gt`, `gteq`, `lteq`, `lt` y `neq`. Los operadores de texto `contains`, `notContains`, `startsWith`, `notStartsWith`, `endsWith` y `notEndsWith` se convierten en predicados `LIKE`.

`between` acepta exactamente dos valores separados por coma y genera límites inferior y superior. `notBetween` lanza una excepción que declara que no está implementado. La conversión explícita de `in` y `notIn` solo se activa si el valor contiene una coma; algunos valores de campo separados por coma también se convierten a `IN`. `NULL`, `null` y el marcador interno `null!` siguen ramas diferentes. En las ramas trazadas se rechazan campos u operadores desconocidos.

Los filtros relacionados usan `table.field`. La tabla debe figurar en la lista de tablas relacionadas configuradas y el campo debe existir en su esquema; después el código agrega un join con alias interno. No es un parámetro general para unir cualquier tabla.

El controlador lee y elimina el parámetro `q`, pero el camino de `get()` trazado no usa su valor para construir una búsqueda. No dependas de búsqueda por `q` en esta versión. Esta sintaxis ilustra el código; las requests no se ejecutaron:

```text
GET /api/v1/products?price[gteq]=25&order[price]=ASC&pageSize=20
GET /api/v1/products?name[contains]=juice&fields=id,name
```

El soporte del Query Builder por sí solo no demuestra que un parámetro esté implementado por este camino API.

## Escrituras y operaciones de registro

`POST` lee el body, valida reglas del esquema y pasa al modelo el modo de ejecución seleccionado. El indicador de body `mode_simulate` solicita simulación para los valores reconocidos; no vuelve seguro ni verificado el flujo completo. `PUT` y `PATCH` comparten `modify()`, pero pasan modos distintos de campos requeridos y consumen `_append` para manejar registros relacionados.

`DELETE` requiere un ID y rechaza IDs múltiples separados por coma, indicando que se use `Collections`. Selecciona borrado lógico solo si el controlador lo habilita y el esquema del modelo contiene el campo correspondiente. El scaffold generado fija el indicador de soft-delete en `true`, pero el efecto depende del modelo, del esquema y de la ruta configurada.

No se ejecutó ninguna petición de creación, actualización, borrado, simulación, escritura de relaciones o autorización.

## Límites de evidencia

`ApiTest.php`, `ProductsBasicTest.php` y `ProductsPaginationTest.php` contienen casos contra la configuración incluida; sus fuentes se inspeccionaron, no se ejecutaron. No prueban una generación limpia de esquema/modelo/API. Cada parámetro, filtro, rama ACL y relación aún requiere evidencia de ejecución antes de presentarse como contrato reproducido.

Consulta la guía de [HTTP/API](/api), [seguridad](/security), el [ciclo de request](/core/request-lifecycle), [descriptores de esquema](/database) y el [constructor DDL](/database/schema-builder). La referencia canónica mantiene el ledger de hallazgos por afirmación.
