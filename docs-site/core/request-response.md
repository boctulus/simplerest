---
title: Request y Response
source:
  - docs/core/request-response.md
  - docs/audit/README.md
source_revision: 85cbc2197c22cb2565a165c9f9ade69b4bd078e4
status: partial
---

# Request y Response

Esta referencia describe los mensajes HTTP y sus helpers. No forma parte del flujo de `CliRouter`; consulta [Arquitectura](/architecture) para el orden de resolvers y [Ciclo de request](/core/request-lifecycle) para la secuencia HTTP. Las clases tienen helpers inspirados en PSR-7, pero no implementan las interfaces PSR-7 completas.

## Request

`Request::getInstance()` devuelve un singleton. En HTTP inicializa la query desde `Url::queryString()`, normaliza headers, lee `php://input` y examina `Content-Type`. Para `application/json` usa `Url::bodyDecode()`; un JSON mal formado genera `InvalidRequestBodyException` con estado 400. En CLI no hace esa lectura HTTP. Query, body y headers se almacenan en propiedades estáticas. `setInstance()` reemplaza y resetea ese estado.

`getHeaders()` pone las claves en minúsculas, traduce `HTTP_*` de `$_SERVER` y añade `CONTENT_TYPE`/`CONTENT_LENGTH`. `header()` y `getHeader()` consultan la clave normalizada; `shiftHeader()` lee y elimina. `getQuery()` devuelve el mapa o una clave; `get()` permite un default y `has()` comprueba presencia. `shiftQuery()` y `shiftBodyParam()` consumen valores.

`getBody()` por defecto convierte el resultado a objeto. `as_array()` cambia ese default; `getBody(false)` solicita un array. `input()` devuelve el body completo o un campo, `getBodyParam()` lee un campo y `getRaw()` conserva el body crudo. `getBodyDecoded()` es una ruta aparte que parsea form-urlencoded o JSON; ante JSON inválido lanza una excepción genérica. El parser JSON de la inicialización usa el error HTTP 400 antes citado.

`getFormData()` devuelve `$_POST`. `parseFormData()` conserva una ruta legacy para JSON dentro de un único campo form-urlencoded; no es el parser multipart ni incluye `$_FILES`.

### Método efectivo y credenciales

`method()` elige `_method` en query si `method_override.by_url` está activo; después consulta `X-HTTP-Method-Override` si `by_header` está activo; si no, usa `$_SERVER['REQUEST_METHOD']`. Cachea el valor en la instancia.

`getAuth()` consume primero `token` en query y lo convierte en `Bearer ...`; como alternativa consulta `authorization` normalizado y las variables de servidor. `getApiKey()` busca `x-api-key` y luego `api_key` en query. `hasAuth()`, `hasApiKey()` e `isAuthenticated()` son ayudas de estado y no validan por sí solas un token. No coloques credenciales en la URL.

**`getTenantId()` tiene una discrepancia de mayúsculas:** el normalizador guarda `x-tenant-id`, pero el método busca `X-TENANT-ID`. La query `tenantid` se consulta primero; el fallback de header no está confirmado y el código actual no encuentra esa clave normalizada.

`acceptsJson()` y `acceptsHtml()` buscan tokens simples en `Accept`; ambos pueden considerar aceptado `*/*`. `acceptEncoding()` lee el header y puede consumirlo; `gzip()`/`deflate()` comprueban los tokens correspondientes. No implementan negociación completa de `q`.

### Copias y parámetros de ruta

`withQueryParam()`, `withoutQueryParam()`, `withHeader()`, `withAddedHeader()`, `withoutHeader()` y `withBody()` devuelven clones. Sus casos se encuentran en pruebas fuente inspeccionadas, no ejecutadas. La copia no vuelve inmutable toda la API: se mantienen getters que consumen estado. `getParam()`/`getParams()` exponen parámetros de ruta; `setParams()` los asigna. `Request` implementa `ArrayAccess` para esos parámetros y `toArray()` devuelve ese mapa.

## Response

`Response::getInstance()` es singleton; `Response::create()` construye otra instancia. Cuando el objeto se destruye, el destructor intenta rollback y cierre de todas las conexiones DB; ten en cuenta el ciclo de vida de una instancia creada explícitamente.

`set()` guarda los datos; `get()` y `__toString()` los exponen. Un array se codifica a JSON si no está marcado para codificación diferida. `send()` prepara el envelope `data`, `status_code` y `error` para arrays/objetos y conserva metadatos adicionales. `sendJson()` y `json()` preparan estado JSON. Estos métodos no equivalen todos a una emisión inmediata. `Response::format()` solo produce un array con el envelope y los extras.

`code()` guarda un código para después; `status()` establece el status HTTP; `sendCode()` prepara JSON mínimo y `sendOK()` intenta establecer 200. `addHeaders()` reemplaza la lista guardada; `addHeader()` y `setHeader()` añaden una línea. `withStatus()`, `withHeader()`, `withAddedHeader()`, `withoutHeader()`, `withBody()` y `withJson()` devuelven clones. `withHeader()` añade una línea, no reemplaza como en PSR-7. La clase ofrece una superficie inspirada, no el contrato completo.

`error()` construye un payload, llama a `flush()` y termina el proceso. `flush()` decide la codificación, imprime el body y ejecuta `exit`. `redirect()` admite 301, 302, 307 o 308; también termina el proceso y lanza si las cabeceras ya se enviaron. Trata estas llamadas como terminales.

## Índice de métodos

`Request`: `as_array`, `getHeaders`, `isBrowser`, `getInstance`, `setInstance`, `getRaw`, `getFormData`, `parseFormData`, `setParams`, `getPaginatorParams`, `headers`, `header`, `getHeader`, `shiftHeader`, `isAjax`, `getAuth`, `hasAuth`, `getApiKey`, `hasApiKey`, `getTenantId`, `hasTenantId`, `authMethod`, `isAuthenticated`, `acceptEncoding`, `gzip`, `deflate`, `getQuery`, `withQueryParam`, `withoutQueryParam`, `withHeader`, `withAddedHeader`, `withoutHeader`, `withBody`, `shiftQuery`, `has`, `get`, `getParam`, `getParams`, `getBody`, `getBodyDecoded`, `getBodyParam`, `input`, `shiftBodyParam`, `getCode`, `offsetSet`, `offsetExists`, `offsetUnset`, `offsetGet`, `method`, `ip`, `user_agent`, `toArray`, `acceptsJson`, `acceptsHtml`.

`Response`: `__destruct`, `getInstance`, `create`, `setInstance`, `offsetExists`, `offsetGet`, `offsetSet`, `offsetUnset`, `jsonSerialize`, `redirect`, `asObject`, `addHeaders`, `addHeader`, `setHeader`, `code`, `setPretty`, `encode`, `setPaginatorParams`, `send`, `sendCode`, `sendOK`, `sendJson`, `error`, `set`, `get`, `__toString`, `isEmpty`, `flush`, `when`, `format`, `status`, `json`, `formatError`, `withStatus`, `withHeader`, `withAddedHeader`, `withoutHeader`, `withBody`, `withJson`.

`RequestImmutableMethodsTest`, `ResponseImmutableMethodsTest`, `RequestInputParsingTest` y `RequestAcceptNegotiationTest` contienen aserciones para casos seleccionados; se inspeccionaron pero no se ejecutaron. La referencia editorial mantiene el ledger de discrepancias y la evidencia de cada afirmación.
