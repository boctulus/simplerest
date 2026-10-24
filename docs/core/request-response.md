# Request y Response

Esta referencia describe las clases de mensaje HTTP y sus helpers públicos. No forma parte del flujo de `CliRouter`; el orden global de los resolvers está en [Arquitectura](../architecture.md) y la secuencia HTTP en [Ciclo de request](request-lifecycle.md). `Request` y `Response` tienen helpers inspirados en PSR-7, pero no implementan las interfaces PSR-7 completas.

## Request: creación y entrada

`Request::getInstance()` devuelve un singleton. En un proceso HTTP inicializa la query desde `Url::queryString()`, normaliza headers, lee `php://input` y examina el `Content-Type`. Para `application/json` usa `Url::bodyDecode()`; un JSON mal formado da `InvalidRequestBodyException` con estado 400. En CLI no hace esa lectura HTTP. Los datos de query, body y headers se guardan en propiedades estáticas compartidas por la instancia. `setInstance()` reemplaza/resetéa ese estado y aparece en fixtures aislados.

`getHeaders()` convierte los nombres a minúsculas, traduce `HTTP_*` de `$_SERVER` y añade `CONTENT_TYPE`/`CONTENT_LENGTH`. `header()` y `getHeader()` consultan la clave normalizada. `headers()` obtiene el mapa; `shiftHeader()` lee y elimina un valor. `isBrowser()` e `isAjax()` son comprobaciones simples de `User-Agent` y `X-Requested-With`, no una detección fiable de identidad ni autorización.

Para leer la query, `getQuery()` devuelve el mapa o un valor por clave, `get()` aplica un valor por defecto y `has()` comprueba la presencia. `shiftQuery()` consume un valor. `shiftBodyParam()` consume un campo del body. Son getters destructivos: el siguiente lector puede ver un mapa distinto.

`getBody()` por defecto convierte el valor a objeto; `as_array()` cambia ese default y `getBody(false)` solicita array explícitamente. `getBodyParam()` lee un campo; `input()` devuelve el array completo cuando no recibe clave o el campo con default cuando sí. `getRaw()` devuelve el cuerpo crudo inicial. `getBodyDecoded()` es otra ruta: intenta decodificar form-urlencoded o JSON desde el raw body y ante JSON inválido lanza una excepción genérica; no debe confundirse con la excepción 400 usada al inicializar el JSON normal.

`getFormData()` devuelve `$_POST`. `parseFormData()` contiene un camino legacy para JSON enviado como un único campo form-urlencoded; no es el parser multipart estándar ni gestiona `$_FILES`. Para multipart, accede a la información de archivos por la ruta de archivos de PHP.

## Request: método, autenticación y negociación

`method()` resuelve el método efectivo en este orden: `_method` de query si `method_override.by_url` está activo; `X-HTTP-Method-Override` si `method_override.by_header` está activo; por último `$_SERVER['REQUEST_METHOD']`. El resultado se cachea en la instancia. La documentación no recomienda colocar credenciales en la query.

`getAuth()` consume primero `token` de query y lo convierte en `Bearer ...`; si falta, consulta `authorization` normalizado y luego las variables de servidor Authorization. `hasAuth()` y `isAuthenticated()` son comprobaciones auxiliares; no sustituyen la validación criptográfica del token. `getApiKey()` revisa `x-api-key` y luego `api_key` en query.

**El fallback de header de `getTenantId()` no coincide con la normalización de headers actual.** `getHeaders()` almacena `x-tenant-id` en minúsculas, mientras `getTenantId()` busca `X-TENANT-ID`. El query `tenantid` sí se consulta primero. La discrepancia está registrada en el [ledger](../audit/README.md#claim-level-findings-request-and-response); no se infiere que el header funcione.

`authMethod()` distingue los nombres `API_KEY` y `JWT`. `acceptsJson()` y `acceptsHtml()` buscan tokens simples dentro del header `Accept`; para `*/*` ambos pueden indicar aceptación. `acceptEncoding()`, `gzip()` y `deflate()` leen codificación, con `acceptEncoding()` consumiendo el header si todavía no se guardó. Estas ayudas no implementan negociación completa de calidad `q`.

## Copias de Request

`withQueryParam()`, `withoutQueryParam()`, `withHeader()`, `withAddedHeader()`, `withoutHeader()` y `withBody()` devuelven clones. Los tests fuente inspeccionados afirman que el objeto original no cambia para esos campos. `withAddedHeader()` une los valores con `, `. La copia no vuelve inmutable toda la API: métodos destructivos como `shiftQuery()` y `shiftHeader()` siguen disponibles y pueden mutar el estado interno de la copia. No es una implementación PSR-7 de `ServerRequestInterface`.

`getParam()`/`getParams()` exponen parámetros de ruta guardados por el ciclo HTTP; `setParams()` los reemplaza. `Request` también implementa `ArrayAccess` sobre esos parámetros y `toArray()` devuelve ese mapa. `getPaginatorParams()` usa los nombres definidos en `config['paginator']['params']` y consume esos query params mediante `shiftQuery()`.

## Response: estado y envelope

`Response::getInstance()` devuelve el singleton; `Response::create()` construye otra instancia. El constructor lee la configuración actual. El destructor intenta ejecutar `DB::rollbackAndCloseAllConnections()`; si falla, registra un mensaje genérico. La duración de una instancia `create()` puede por eso tener efectos sobre las conexiones al destruirse.

`set()` guarda los datos; `get()` y `__toString()` devuelven el cuerpo. Si hay un array y aún no está marcado para codificación, `get()` lo convierte a JSON. `jsonSerialize()` devuelve el estado de datos actual, pero la clase no declara implementar `JsonSerializable`.

`send($data, $http_code)` prepara un envelope para arrays/objetos con `data`, `status_code` y `error`; conserva claves adicionales del caller salvo `data`, `status_code` y `error`. Si `$http_code >= 400`, delega a `error()`, que construye el payload de error, llama a `flush()` y termina el proceso. `sendJson()` prepara el mismo envelope y marca la respuesta para codificación JSON. `json()` también prepara un envelope JSON. Estos helpers preparan estado, pero no todos envían el cuerpo inmediatamente.

`code()` guarda un código para uso posterior; `status()` emite el código HTTP si es posible y conserva el valor en la instancia. `sendCode()` guarda una respuesta JSON mínima con el código y establece el status, mientras `sendOK()` intenta establecer 200. `Response::format()` es una función estática que solo devuelve un array con `data`, `status_code`, `error` y extras. `setPaginatorParams()` aplica el formatter configurado para la respuesta. La relación entre metadatos, `Paginator` y límites de Query Builder está en [Paginación](../database/pagination.md).

`addHeaders()` reemplaza el arreglo interno; `addHeader()` y `setHeader()` agregan una línea de header. `withStatus()`, `withHeader()`, `withAddedHeader()`, `withoutHeader()`, `withBody()` y `withJson()` devuelven clones. Los helpers de header de respuesta agregan líneas, incluso `withHeader()`; no ofrece semántica completa de reemplazo PSR-7. `withStatus()` valida códigos entre 100 y 599; el reason phrase se guarda, pero la emisión usa la API de status de PHP.

## Emisión y terminación

`flush()` elige el cuerpo según el estado y el header `Accept`. Si la respuesta está marcada para codificar, usa JSON. Si los datos son un array y todavía no lo están, también los codifica como JSON. Si el body es un string JSON bajo `Accept: application/json`, evita serializarlo dos veces. Después imprime la respuesta y llama a `exit`. `error()` y `redirect()` también terminan el proceso; trata esas ramas como terminales en los controladores.

`redirect($url, $http_code)` solo admite 301, 302, 307 y 308; si los headers ya fueron enviados, lanza excepción. `setPretty()` controla `JSON_PRETTY_PRINT`; las opciones JSON base conservan Unicode y slashes sin escapar. No se ejecutó una respuesta HTTP en este audit.

## Índice de métodos

Los métodos públicos identificados en `Request` son: `as_array`, `getHeaders`, `isBrowser`, `getInstance`, `setInstance`, `getRaw`, `getFormData`, `parseFormData`, `setParams`, `getPaginatorParams`, `headers`, `header`, `getHeader`, `shiftHeader`, `isAjax`, `getAuth`, `hasAuth`, `getApiKey`, `hasApiKey`, `getTenantId`, `hasTenantId`, `authMethod`, `isAuthenticated`, `acceptEncoding`, `gzip`, `deflate`, `getQuery`, `withQueryParam`, `withoutQueryParam`, `withHeader`, `withAddedHeader`, `withoutHeader`, `withBody`, `shiftQuery`, `has`, `get`, `getParam`, `getParams`, `getBody`, `getBodyDecoded`, `getBodyParam`, `input`, `shiftBodyParam`, `getCode`, `offsetSet`, `offsetExists`, `offsetUnset`, `offsetGet`, `method`, `ip`, `user_agent`, `toArray`, `acceptsJson`, `acceptsHtml`.

Los métodos públicos identificados en `Response` son: `__destruct`, `getInstance`, `create`, `setInstance`, `offsetExists`, `offsetGet`, `offsetSet`, `offsetUnset`, `jsonSerialize`, `redirect`, `asObject`, `addHeaders`, `addHeader`, `setHeader`, `code`, `setPretty`, `encode`, `setPaginatorParams`, `send`, `sendCode`, `sendOK`, `sendJson`, `error`, `set`, `get`, `__toString`, `isEmpty`, `flush`, `when`, `format`, `status`, `json`, `formatError`, `withStatus`, `withHeader`, `withAddedHeader`, `withoutHeader`, `withBody`, `withJson`.

Los nombres son un índice de declaraciones en el checkout, no una afirmación de que cada rama esté cubierta por una ejecución. `RequestImmutableMethodsTest`, `ResponseImmutableMethodsTest`, `RequestInputParsingTest` y `RequestAcceptNegotiationTest` se inspeccionaron; no se ejecutaron. El ciclo de despacho y los puntos que llaman a `flush()` se explican en [Ciclo de request](request-lifecycle.md).
