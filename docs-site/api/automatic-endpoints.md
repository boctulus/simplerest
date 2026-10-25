---
title: Resolución y parámetros de endpoints automáticos
source:
  - docs/api/automatic-endpoints.md
  - docs/audit/README.md
source_revision: 41df667118f065fc7b22f6f28f6274a35de5179c
---

# Endpoints REST automáticos

SimpleRest ofrece controladores REST reutilizables con filtros, paginación, ordenamiento, agregaciones y relaciones. Para exponer una tabla existente, genera los artefactos PHP y configura la conexión de base de datos, la autenticación y los permisos.

## Resolución del recurso

Con `remove_api_slug = false`, `ApiHandler::resolve()` espera después del segmento `api` otro segmento con forma de versión. Para un slug ordinario deriva una clase de aplicación dentro del namespace configurado `Controllers\\api`; `trashcan` y `collections` tienen mapeos especiales a controladores core. El resolver devuelve la clase destino, el método, los argumentos y la versión. No consulta los esquemas de base de datos ni crea clases de controlador.

`FrontController::resolve()` verifica que existan la clase y el método (con la excepción del método mágico `__call`). Para requests API que no sean de autenticación también requiere que el método esté incluido en `getCallable()` antes del despacho.

## Crear un recurso REST

Habilita el [paquete de scaffolding](/packages/scaffolding) y genera el descriptor del esquema, el modelo y el controlador API de una tabla existente:

```bash
php com make any products --schema --model --api --from=main
```

También puedes usar por separado `php com make schema products`, `php com make model products` y `php com make api products`. El generador usa la plantilla `ApiRestfulController`, que hereda de `ApiController` y proporciona las operaciones GET, POST, PUT, PATCH y DELETE sin escribir cada operación manualmente.

Si el controlador no indica un modelo, `ApiController` lo deriva del nombre del controlador. El esquema describe campos y relaciones, pero un descriptor aislado no registra una clase de controlador. El controlador generado habilita la opción de borrado lógico; para aplicarlo, el esquema debe contener el campo correspondiente.

## Métodos HTTP y autorización

El resolver usa el método HTTP como nombre de método destino. El `ApiController` genérico implementa `get`, `post`, `put`, `patch` y `delete`; `head()` llama a `get()` con buffering de salida y `options()` está vacío. El constructor crea la lista invocable a partir del ACL de tabla o recurso, y el front controller la comprueba antes de despachar un método API que no sea de autenticación. Que un método exista no significa que cualquier llamador pueda invocarlo.

El resolver valida que el path tenga un segmento con forma de versión y lo devuelve junto con el controlador, método y argumentos. Esta ruta no selecciona una implementación de controlador por versión. Los slugs se normalizan de snake_case a PascalCase; `trashcan` y `collections` se asignan a clases API core. 

## Parámetros GET de colecciones

`ApiController::get()` consume los parámetros siguientes antes de llamar al modelo o Query Builder:

| Parámetro | Comportamiento y restricciones |
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
| `having` | Analiza comparaciones acotadas de función/campo o alias contra un literal numérico. La paginación de resultados agrupados con HAVING no está implementada. |
| `include`, `_related` | Entran en el camino de subrecursos si hay relaciones configuradas. `include` reduce ese conjunto; deben estar configuradas las relaciones correspondientes. |
| `folder` | Selecciona una rama con búsqueda de carpeta y comprobaciones ACL; requiere configurar la carpeta y sus permisos ACL. |

Los parámetros ordinarios de colección pasan a filtros del modelo después de una conversión específica de API. Los operadores de comparación son `eq`, `gt`, `gteq`, `lteq`, `lt` y `neq`. Los operadores de texto `contains`, `notContains`, `startsWith`, `notStartsWith`, `endsWith` y `notEndsWith` se convierten en predicados `LIKE`.

`between` acepta exactamente dos valores separados por coma y genera límites inferior y superior. `notBetween` lanza una excepción que declara que no está implementado. La conversión explícita de `in` y `notIn` solo se activa si el valor contiene una coma; algunos valores de campo separados por coma también se convierten a `IN`. `NULL`, `null` y el marcador interno `null!` siguen ramas diferentes. Se rechazan campos u operadores desconocidos.

Los filtros relacionados usan `table.field`. La tabla debe figurar en la lista de tablas relacionadas configuradas y el campo debe existir en su esquema; después el código agrega un join con alias interno. No es un parámetro general para unir cualquier tabla.

El controlador lee y elimina el parámetro `q`, pero no lo utiliza para buscar. Usa filtros específicos por campo. Ejemplos:

```text
GET /api/v1/products?price[gteq]=25&order[price]=ASC&pageSize=20
GET /api/v1/products?name[contains]=juice&fields=id,name
```


## Escrituras y operaciones de registro

`POST` lee el body, valida reglas del esquema y pasa al modelo el modo de ejecución seleccionado. El indicador de body `mode_simulate` solicita simulación para los valores reconocidos; no vuelve seguro ni verificado el flujo completo. `PUT` y `PATCH` comparten `modify()`, pero pasan modos distintos de campos requeridos y consumen `_append` para manejar registros relacionados.

`DELETE` requiere un ID y rechaza IDs múltiples separados por coma, indicando que se use `Collections`. Selecciona borrado lógico solo si el controlador lo habilita y el esquema del modelo contiene el campo correspondiente. El scaffold generado fija el indicador de soft-delete en `true`, pero el efecto depende del modelo, del esquema y de la ruta configurada.


## Guías relacionadas

Consulta [HTTP/API](/api), [seguridad](/security), [ciclo de la petición](/core/request-lifecycle), [descriptores de esquema](/database), [migraciones](/database/migrations) y [scaffolding](/packages/scaffolding).
