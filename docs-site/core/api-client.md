---
title: ApiClient
source:
  - docs/core/api-client.md
  - docs/audit/README.md
source_revision: 683d89509c036f2495e7ba22b2b8a4128273f95c
status: partial
---

# ApiClient

`ApiClient` es el cliente HTTP saliente de `Boctulus\Simplerest\Core\Libs`. Usa cURL; el constructor crea un handle y `consumeAPI()` comprueba la extensión. Esta referencia se basa en el código actual. No se ejecutó ninguna petición de red.

## Crear y configurar

Se puede crear con `new ApiClient($url)` o `ApiClient::instance($url)`. `setUrl()`/`url()` cambian la URL y `setBaseUrl()` guarda una base sin la barra final. `get()`, `delete()`, `post()`, `put()` y `patch()` devuelven el cliente para encadenar la configuración y la petición. Si la URL del helper empieza por `/` y hay una base configurada, concatena ambas. `queryParam()` y `queryParams()` agregan parámetros.

```php
$client = ApiClient::instance('https://service.example')
    ->accept('application/json')
    ->get('/v1/items');

$response = $client->getResponse();
```

El ejemplo muestra la forma de llamada; no se contactó ese servicio.

`request($url, $http_verb, $body, $headers, $options)` es el camino compartido. `setMethod()` permite únicamente `GET`, `POST`, `PUT`, `PATCH` y `DELETE`. Aunque existe `HTTP_METH_HEAD`, `setMethod('HEAD')` lo rechaza. `QUERY` no está implementado: hay un test fuente que lo espera, pero el método y la constante no están en esta versión.

## Headers, cuerpo y cURL

`addHeader()`, `setHeaders()` y `removeHeader()` administran los headers. `contentType()`, `accept()`, `userAgent()` y `authorization()` son helpers; `setUserAgent()` configura una opción de cURL. `setBody($body, $encoded = true)` guarda el cuerpo y su bandera de codificación. Con un array y la bandera activa, el cuerpo se convierte a JSON.

`setOption()`/`option()` y `setOptions()` configuran opciones de cURL; `followLocations()` y `redirect()` controlan redirecciones. `mock()` guarda una respuesta y permite que `request()` retorne sin invocar el transporte.

**Los setters de timeout no limitan la petición normal en el código actual.** `setTimeOut()` y `setConnectionTimeOut()` guardan opciones, pero `consumeAPI()` aplica `CURLOPT_TIMEOUT` y `CURLOPT_CONNECTTIMEOUT` con valor cero después de esas opciones. No se probó el efecto en red.

## Respuesta, errores y reintentos

`request()` guarda el status HTTP, los headers, el tipo de contenido, la URL efectiva, errores cURL y el cuerpo. `getStatus()`/`status()` devuelven el status; `getHeaders()`, `getContentType()` y `getEffectiveUrl()` exponen metadatos. `getRawResponse()` y `data(true)` dan el cuerpo crudo.

`data()` decodifica JSON a array cuando `setDecode(true)`/`decode(true)` lo habilitan y la respuesta es JSON. `getResponse()` devuelve `data`, `http_code` y `error`; según content type o la bandera de decode, decodifica JSON/XML. `getBody()` es alias de `data()`.

`error()`/`getError()` reportan errores de transporte cURL, no clasifican por sí solos los status 4xx/5xx. `getDataOrFail()` lanza si existe ese error. Comprueba `getStatus()` por separado. `setRetries($qty)` configura intentos ante error de transporte; por defecto, el bucle permite un intento. No se afirma reintento por status HTTP.

## Caché, autenticación y certificados

`setCache()`/`cache()` establecen expiración; `readOnly()` impide guardar una nueva respuesta y `enablePostRequestCache()` habilita cachear POST. Por defecto, `request()` guarda respuestas sin error con status 200–399. `ignoreStatusCodes()` agrega status a guardar. La implementación usa `FileCache`; no se comprobó colisión de claves ni coherencia entre verbos.

`setBasicAuth()` agrega Basic y `setJWTAuth()` agrega Bearer. `disableSSL()`/`withoutStrictSSL()` desactivan la verificación TLS del host y del certificado; no son adecuados para conexiones de producción. `setSSLCrt()`/`certificate()` configuran una ruta de certificado.

## Descargas y superficie restante

`download()` y `simpleFtpGet()` forman parte de la clase. `download()` ejecuta dos llamadas consecutivas a `curl_exec()` sobre el mismo handle; no se prescribe su uso hasta revisar esa anomalía.

También existen `logReq()`, `logRes()`, `showRequest()`, `dump()`, `debug()`, `getResource()`, `close()`, `setCookieOptions()`, `useCookieJar()`, `setBinary()`, `getFilename()`, `getCachePath()`, `httpHeader()`, `httpMethod()`, `httpLogin()`, `proxy()`, `proxyLogin()`, `isEnabled()`, y `when()`.

## Inventario de métodos

Índice de los métodos públicos declarados. La presencia en la lista no equivale a una validación funcional.

| Área | Métodos |
| --- | --- |
| Creación y petición | `__construct`, `instance`, `setUrl`, `url`, `setBaseUrl`, `request`, `get`, `delete`, `post`, `put`, `patch`, `setMethod`, `send`, `exec` |
| Headers, cuerpo y transporte | `queryParam`, `queryParams`, `addHeader`, `setHeaders`, `removeHeader`, `httpHeader`, `setBody`, `getBody`, `contentType`, `accept`, `userAgent`, `setUserAgent`, `authorization`, `setOption`, `option`, `setOptions`, `followLocations`, `redirect`, `setTimeOut`, `setConnectionTimeOut`, `setRetries`, `consumeAPI`, `getRequestHeaders`, `getResource`, `close` |
| Respuesta y simulación | `mock`, `showRequest`, `dump`, `debug`, `setDecode`, `decode`, `noDecode`, `getRawResponse`, `getStatus`, `status`, `getError`, `error`, `data`, `getDataOrFail`, `getResponse`, `getHeaders`, `getContentType`, `getEffectiveUrl`, `getFilename`, `setBinary` |
| Caché, cookies, auth y red | `readOnly`, `setCache`, `cache`, `cacheUntil`, `clearCache`, `enablePostRequestCache`, `getCachePath`, `useCookieJar`, `setCookieOptions`, `setBasicAuth`, `setJWTAuth`, `disableSSL`, `withoutStrictSSL`, `setSSLCrt`, `certificate`, `download`, `simpleFtpGet`, `httpMethod`, `httpLogin`, `proxy`, `proxyLogin`, `isEnabled`, `logReq`, `logRes`, `ignoreStatusCodes`, `when` |

Las constantes `HTTP_METH_POST`, `HTTP_METH_GET`, `HTTP_METH_PATCH`, `HTTP_METH_PUT`, `HTTP_METH_DELETE` y `HTTP_METH_HEAD` existen; `HTTP_METH_HEAD` no añade HEAD a la allowlist de `setMethod()`. `HTTP_METH_QUERY` no existe en este checkout.

`HttpQuerySupportTest.php` espera un método `QUERY` que no coincide con el código. Se inspeccionó la fuente del test, no se ejecutó. Ninguna petición real ni respuesta externa se reprodujo en esta revisión.
