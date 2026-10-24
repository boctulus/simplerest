---
title: Requests cross-origin (CORS)
source:
  - docs/core/cors.md
source_revision: 7fbd805801feee1e927c726a51ec2c68f3a12bfe
status: partial
---

# Requests cross-origin (CORS)

SimpleRest incluye `CorsHandler` y el helper global `cors()`. Bootstrap y `FrontController` no los instalan de forma global. El constructor HTTP de `Api\ResourceController` invoca `cors()`; los demás caminos deben llamar explícitamente al helper o al handler.

## Configuración y coincidencia de ruta

`cors()` carga `config/cors.php`, crea `CorsHandler`, aplica el array y llama a `handle()`. La configuración incluida usa la ruta `api/*`, origen, métodos y headers wildcard, sin headers expuestos, edad máxima cero y credenciales desactivadas.

El handler obtiene el path de `REQUEST_URI` y lo compara con `fnmatch()`. El patrón incluido no comienza con `/`, mientras que un path HTTP normal sí. Las pruebas focalizadas usan `*`, no el preset del archivo; no está verificado que `api/*` coincida con `/api/...`. Comprueba ese patrón antes de depender de él.

Si el path no coincide o falta `HTTP_ORIGIN`, el handler retorna sin modificar headers. No usa `Referer` como sustituto de Origin. Acepta origen exacto, `*` o expresiones regulares en `allowedOriginsPatterns`. No permite combinar origen wildcard con credenciales; esa configuración lanza `InvalidArgumentException`.

## Headers y preflight

Para un origen permitido emite `Access-Control-Allow-Origin`, `Access-Control-Allow-Credentials` y, si están configurados, headers/métodos permitidos y expuestos. La lista wildcard de métodos se expande a GET, POST, PUT, PATCH, DELETE y OPTIONS. El handler agrega esos headers, pero no compara `Access-Control-Request-Method` ni `Access-Control-Request-Headers` con la solicitud antes de responder.

Un OPTIONS con origen permitido establece 204 y `Content-Length: 0`, y luego termina con `exit`. Un origen rechazado establece 403 y lanza `RuntimeException` con código 403; el caller determina cómo se presenta el error.

## Verificación y límites

`CorsHandlerOriginTest.php` afirma que Referer no sustituye a un Origin ausente y que un origen rechazado lanza código 403. Se inspeccionó, no se ejecutó. No se reprodujo una request CORS ni un preflight. El patrón de ruta predeterminado no está verificado con una ruta real.
