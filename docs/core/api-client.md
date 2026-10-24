# ApiClient

`ApiClient` es el cliente HTTP saliente de `Boctulus\Simplerest\Core\Libs`. El código usa cURL; el constructor crea un handle y `consumeAPI()` verifica la extensión. Esta referencia está trazada contra el checkout actual. No se ejecutó ninguna petición de red.

## Crear y configurar el cliente

Se puede crear con `new ApiClient($url)` o mediante [`ApiClient::instance($url)`](../../src/framework/Libs/ApiClient.php#L269). El constructor normaliza la URL y crea el handle cURL; `setUrl()`/`url()` cambian la URL y `setBaseUrl()` guarda una base sin la barra final.

`get()`, `delete()`, `post()`, `put()` y `patch()` devuelven el mismo cliente para encadenar configuración y petición. Si el argumento de URL empieza por `/` y se configuró una base, esos helpers concatenan la ruta a la base. `queryParam()` y `queryParams()` agregan parámetros a la URL de la petición. La forma de uso siguiente es ilustrativa: no se contactó ningún servicio externo.

```php
$client = ApiClient::instance('https://service.example')
    ->accept('application/json')
    ->get('/v1/items');

$response = $client->getResponse();
```

`request($url, $http_verb, $body, $headers, $options)` es el camino compartido por los helpers. `setMethod()` selecciona un verbo para `send()`, pero su allowlist actual solo acepta `GET`, `POST`, `PUT`, `PATCH` y `DELETE`. La clase declara además `HTTP_METH_HEAD`, pero `setMethod('HEAD')` lo rechaza. `QUERY` tampoco está implementado en el código actual: una prueba fuente espera el método y una constante que no existen. La discrepancia está registrada en el [ledger de auditoría](../audit/README.md#claim-level-findings-apiclient); la prueba no se ejecutó.

## Headers, cuerpo y opciones

`addHeader()`, `setHeaders()` y `removeHeader()` administran headers. `contentType()`, `accept()`, `userAgent()` y `authorization()` son helpers para headers; `setUserAgent()` configura la opción de cURL. `setBody($body, $encoded = true)` guarda el cuerpo y su bandera de codificación. Si el cuerpo es un array y la bandera está activa, `consumeAPI()` lo codifica como JSON antes de enviarlo.

`setOption()`/`option()` agregan una opción cURL; `setOptions()` reemplaza o combina el conjunto existente según los valores de entrada. `followLocations()` y `redirect()` configuran redirecciones. `when()` aplica uno de dos callbacks de configuración según una condición. `mock()` guarda una respuesta de prueba y hace que `request()` retorne el cliente sin llamar al transporte.

**Los setters de timeout no limitan la petición normal en el código actual.** `setTimeOut()` y `setConnectionTimeOut()` agregan opciones, pero `consumeAPI()` fija `CURLOPT_TIMEOUT` y `CURLOPT_CONNECTTIMEOUT` a cero después de aplicar las opciones. El ledger registra esta diferencia como anomalía de implementación; no se probó en la red.

## Respuesta y errores

`request()` conserva status HTTP, headers de respuesta, content type, URL efectiva, error de cURL y cuerpo. `getStatus()`/`status()` devuelven el status; `getHeaders()`, `getContentType()` y `getEffectiveUrl()` exponen metadatos; `getRawResponse()` y `data(true)` devuelven la respuesta cruda.

`data()` decodifica JSON a array solo cuando se activa `setDecode(true)`/`decode(true)` y el contenido es JSON. `getResponse()` devuelve un array con `data`, `http_code` y `error`; puede decodificar JSON por content type y JSON/XML según el content type o la bandera de decode. `getBody()` es un alias de `data()`, no una conversión adicional del cuerpo.

`error()` y `getError()` reflejan el mensaje de error de cURL, no una clasificación general de status HTTP. `getDataOrFail()` lanza si ese mensaje existe; no convierte por sí solo todo status 4xx/5xx en excepción. Revisa `getStatus()` para distinguir status de transporte. `setRetries($qty)` ajusta el límite del bucle de intentos ante error de transporte; el valor predeterminado es 1 intento, no una prueba de reintentos ante status HTTP.

## Cache y autenticación

`setCache()`/`cache()` definen expiración, `readOnly()` evita guardar una respuesta nueva y `enablePostRequestCache()` permite considerar respuestas POST. En `request()`, por defecto se guardan responses sin error con status entre 200 y 399; `ignoreStatusCodes()` permite agregar otros status a la lista de guardado. La caché usa `FileCache`. No se verificaron colisiones de claves ni la coherencia de caché entre todos los verbos.

`setBasicAuth()` agrega un header Authorization Basic; `setJWTAuth()` establece un header Bearer. No incluyas secretos en ejemplos, logs o reportes compartidos. `disableSSL()` y su alias `withoutStrictSSL()` desactivan `CURLOPT_SSL_VERIFYHOST` y `CURLOPT_SSL_VERIFYPEER`; no son opciones seguras para conexiones de producción. `setSSLCrt()`/`certificate()` configuran una ruta de certificado.

## Descargas y API de conveniencia

`download()` y `simpleFtpGet()` aparecen en la superficie de la clase. `download()` ejecuta dos llamadas consecutivas a `curl_exec()` sobre el mismo handle; por eso esta página no prescribe una receta de descarga. El comportamiento debe revisarse antes de depender de él. Esta anomalía y el estado de `setMethod()`/timeouts están en el [ledger](../audit/README.md#claim-level-findings-apiclient).

La clase también expone `logReq()`, `logRes()`, `showRequest()`, `dump()`, `debug()`, `getResource()`, `close()`, `setCookieOptions()`, `useCookieJar()`, `setBinary()`, `getFilename()`, `getCachePath()`, `httpHeader()`, `httpMethod()`, `httpLogin()`, `proxy()`, `proxyLogin()` e `isEnabled()`. El inventario de nombres no equivale a una validación funcional de cada helper.

## Inventario de métodos

Este índice refleja los métodos declarados en la clase y conserva sus nombres actuales. Los métodos protegidos de caché/respuesta se omiten.

| Área | Métodos |
| --- | --- |
| Creación y petición | `__construct`, `instance`, `setUrl`, `url`, `setBaseUrl`, `request`, `get`, `delete`, `post`, `put`, `patch`, `setMethod`, `send`, `exec` |
| Headers, cuerpo y transporte | `queryParam`, `queryParams`, `addHeader`, `setHeaders`, `removeHeader`, `httpHeader`, `setBody`, `getBody`, `contentType`, `accept`, `userAgent`, `setUserAgent`, `authorization`, `setOption`, `option`, `setOptions`, `followLocations`, `redirect`, `setTimeOut`, `setConnectionTimeOut`, `setRetries`, `consumeAPI`, `getRequestHeaders`, `getResource`, `close` |
| Respuesta y simulación | `mock`, `showRequest`, `dump`, `debug`, `setDecode`, `decode`, `noDecode`, `getRawResponse`, `getStatus`, `status`, `getError`, `error`, `data`, `getDataOrFail`, `getResponse`, `getHeaders`, `getContentType`, `getEffectiveUrl`, `getFilename`, `setBinary` |
| Cache, cookies, auth y red | `readOnly`, `setCache`, `cache`, `cacheUntil`, `clearCache`, `enablePostRequestCache`, `getCachePath`, `useCookieJar`, `setCookieOptions`, `setBasicAuth`, `setJWTAuth`, `disableSSL`, `withoutStrictSSL`, `setSSLCrt`, `certificate`, `download`, `simpleFtpGet`, `httpMethod`, `httpLogin`, `proxy`, `proxyLogin`, `isEnabled`, `logReq`, `logRes`, `ignoreStatusCodes`, `when` |

`HTTP_METH_POST`, `HTTP_METH_GET`, `HTTP_METH_PATCH`, `HTTP_METH_PUT`, `HTTP_METH_DELETE` y `HTTP_METH_HEAD` son constantes declaradas. La constante `HTTP_METH_HEAD` no cambia la allowlist de `setMethod()` y la constante `HTTP_METH_QUERY` no existe en este checkout.

## Evidencia y límites

`HttpQuerySupportTest.php` contiene una expectativa para el método `QUERY` que no coincide con el código; se inspeccionó, no se ejecutó. Otros tests usan `ApiClient` para llamadas API, pero no se reprodujo una petición externa ni se verificó una respuesta real en esta revisión. Los detalles de tests, timeouts, verbos y descarga están clasificados en el [ledger de ApiClient](../audit/README.md#claim-level-findings-apiclient).
