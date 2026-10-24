---
title: Controladores API de recursos
source:
  - docs/api/resource-controllers.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# Controladores API de recursos

Esta referencia describe el comportamiento común y los puntos de extensión de los controladores de recursos. La resolución automática, los parámetros GET y el CRUD genérico están en [Endpoints automáticos](/api/automatic-endpoints). El recorrido HTTP está separado en [su propia guía](/core/request-lifecycle); CORS pertenece a [la referencia del core](/core/cors).

## Construcción del controlador

`ResourceController` es la base HTTP de los recursos API incorporados. En CLI su constructor retorna inmediatamente. En HTTP llama a `cors()`, emite los headers declarados por la subclase y ejecuta `response()->sendOK()` para `OPTIONS`. Ese método sólo fija el estado 200 y devuelve el objeto de respuesta: no envía ni termina la petición. Un preflight CORS permitido puede finalizar antes, dentro de `CorsHandler`.

`ApiController` extiende esa clase. Deriva el modelo y la tabla del nombre del controlador, salvo que la subclase indique esos valores; selecciona la conexión tenant cuando recibe un tenant ID y prepara la lista de métodos invocables según el método HTTP efectivo y el ACL. `FrontController` consulta esa lista antes de despachar la acción API. Para entonces el constructor ya se ejecutó. Consulta [Autenticación](/security/authentication) y [ACL](/security/acl) para esos controles.

El constructor de `ApiController` también llama a tipos de impersonación que no están definidos en este repositorio. Por eso esta traza no afirma que cualquier aplicación independiente pueda construir el controlador con sólo instalar SimpleRest.

## Acciones y formato de salida

El controlador genérico declara `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD` y `OPTIONS`. `HEAD` ejecuta `GET` con buffering y descarta el cuerpo; la acción `OPTIONS` está vacía. El constructor puede atender antes un preflight CORS permitido.

`ApiController` no define una interfaz de serialización propia. Algunas acciones entregan arrays u otros valores a `Response`. `OutputHandler` elige JSON para un `ApiController` sólo cuando una acción retorna un valor no nulo al `FrontController`. Las llamadas directas a `response()->send()` recorren la ruta del objeto de respuesta. Consulta [Request y Response](/core/request-response).

Una subclase puede declarar `static::$hidden` y activar `static::$hide_in_response` para quitar atributos nombrados de respuestas simples de un registro o una lista. El filtrado no se aplica actualmente a los subrecursos; tampoco es una regla ACL por campo ni cambia los datos almacenados.

## Hooks de extensión

Los hooks protegidos permiten ampliar las ramas genéricas. Los hooks de carpetas sólo intervienen cuando se usa una rama de carpeta.

| Operación | Familias de hooks |
| --- | --- |
| Lectura | `onGettingBeforeCheck`, `onGettingAfterCheck`, `onGettingAfterCheck2`, `onCounting`, `onGot` |
| Creación | `onPostingBeforeCheck`, `onPostingAfterCheck`, `onPost` |
| Actualización | `onPuttingBeforeCheck`, `onPuttingBeforeCheck2`, `onPuttingAfterCheck`, `onPut` |
| Eliminación | `onDeletingBeforeCheck`, `onDeletingAfterCheck`, `onDeleted` |
| Carpetas | familias `onGettingFolder*`, `onDeletingFolder*`, `onPostingFolder*` y `onPuttingFolder*` |

Los hooks de escritura reciben datos por referencia cuando su firma incluye `&$data`. El orden depende de cada rama: comprueba el método concreto antes de asumir que todos los hooks corren en cada petición. La publicación de eventos CRUD se describe en [Webhooks](/webhooks).

## Alcance de verificación

La página se trazó desde el código. No se ejecutaron peticiones API, serialización de respuestas ni operaciones CRUD respaldadas por base de datos. La presencia de un hook tampoco demuestra que sea seguro para una escritura concreta.
