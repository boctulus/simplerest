---
title: "Webhooks: seguridad de callbacks y firma HMAC"
current_step: 8
next_step: null
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: null
tags: [webhooks, security, ssrf, hmac, secrets]
---

## Objetivo

Endurecer los callbacks configurables por usuarios o integraciones para que el motor no pueda utilizarse como vector SSRF y para que el receptor pueda verificar autenticidad e integridad de cada entrega.

## Pasos planificados

1. **Auditar superficie de ataque** — relevar validación actual de `callback`, redirects, DNS resolution, protocolos permitidos, timeouts y manejo de secretos/logs.
2. **Definir política de endpoints** — permitir únicamente esquemas soportados y bloquear localhost, loopback, link-local, redes privadas, metadata endpoints y destinos internos no autorizados; contemplar DNS rebinding y redirects.
3. **Agregar secreto por subscription** — generar/almacenar un secreto de entrega de forma que no se exponga en respuestas o logs innecesarios y definir mecanismo de rotación.
4. **Firmar el cuerpo exacto** — definir headers de event id, timestamp y firma; calcular HMAC-SHA256 sobre `timestamp + "." + raw_body` o contrato equivalente documentado y versionado.
5. **Definir verificación y tolerancia temporal** — documentar algoritmo para consumidores, comparación constant-time y ventana contra replay de requests firmados.
6. **Asegurar el cliente HTTP** — timeouts, límites de redirects/body, TLS verificable y sanitización de errores antes de persistir/loggear.
7. **Agregar pruebas de seguridad** — cubrir loopback IPv4/IPv6, rangos privados, redirect a red privada, DNS host permitido/rechazado, firma válida/inválida y rotación de secreto.
8. **Actualizar documentación pública** — incluir ejemplo de verificación de firma y reglas de endpoints aceptados.

## Criterios de aceptación

- No se pueden registrar o alcanzar callbacks hacia destinos internos prohibidos mediante resolución directa o redirects.
- Cada subscription puede autenticar sus deliveries mediante una firma verificable.
- Los secretos no aparecen en payloads, logs ni errores normales.
- La firma usa el raw body realmente transmitido y un timestamp incluido en el contrato.
- La política de red y el algoritmo de firma están cubiertos por pruebas automatizadas.

## Auditoría completada — paso 1 (2026-10-04)

- `WebhooksSchema` valida `callback` solo como string requerido de hasta 255 caracteres. No aplica la regla `url` disponible en `Validator` ni valida esquema, IP resuelta o redes privadas. `WebhooksModel::$hidden` está vacío; todavía no existe un secreto por subscription.
- `WebhookHttpTransport::send()` delega a `consume_api($callback, 'POST', $payload)`. Para callback que empieza con `/`, `consume_api()` antepone `base_url()`; los demás se pasan al `ApiClient`.
- `ApiClient::setUrl()` llama a `Url::normalize()`. Esa normalización solo comprueba que el texto empiece por `http`, no exige exactamente `http`/`https` ni aplica política DNS/IP; reconstruye la URL con esquema, host, puerto, path y query. No hay resolución previa, comprobación de rangos ni pinning de direcciones.
- `consume_api()` llama a `withoutStrictSSL()`, que establece `CURLOPT_SSL_VERIFYHOST = 0` y `CURLOPT_SSL_VERIFYPEER = 0`. Este es el riesgo de TLS observado en la auditoría anterior.
- La ruta del webhook no pasa opciones a `consume_api()`, no llama a `followLocations()` y no establece `CURLOPT_FOLLOWLOCATION`; el seguimiento de redirects no se activa con la configuración actual. El helper genérico sí ofrece `followLocations()`, pero no se usa aquí.
- La ejecución de `ApiClient` configura timeout y connect-timeout en `0` (sin límite) y devuelve el cuerpo completo de cURL en memoria; no se configura un límite de respuesta para esta ruta.
- No hay firma HMAC ni secreto en el envelope. `WebhookDeliveryDispatcher` envía el payload sin headers de firma; el resultado del transporte se ignora. El logging de request/response de `ApiClient` es opt-in y `consume_api()` no lo activa.
- La auditoría fue estática: no se registraron subscriptions de prueba ni se hicieron requests a callbacks.

Referencias: `app/Schemas/main/WebhooksSchema.php`, `app/Models/main/WebhooksModel.php`, `src/framework/Helpers/url.php` (`consume_api`), `src/framework/Libs/ApiClient.php` (`setUrl`, `followLocations`, `withoutStrictSSL`, opciones cURL), `src/framework/Libs/Url.php` (`normalize`), `src/framework/Libs/WebhookHttpTransport.php` y `src/framework/Libs/WebhookDeliveryDispatcher.php`.

## Política de endpoints — paso 2

- Solo se aceptan callbacks absolutos `https://` y, fuera de producción, `http://`. Si `app_env` vale `prod` o `production`, el callback debe usar HTTPS; HTTP, rutas relativas, otros esquemas, userinfo (`usuario:clave@host`) y fragmentos se rechazan. En los demás entornos se permite HTTP además de HTTPS, pero se mantienen las mismas restricciones de destinos. Se conserva el path, puerto y query necesarios para el endpoint. Para HTTPS se verifican siempre el certificado y el nombre del host.
- Se permiten únicamente destinos de Internet globalmente alcanzables. Para un literal IPv4/IPv6 se clasifica esa dirección; para un hostname se resuelve A y AAAA y se evalúa el conjunto completo después de seguir CNAME. Si hay respuestas mixtas, el conjunto final no contiene direcciones A/AAAA, hay un error de resolución o aparece un tipo de dirección no reconocido, la operación falla cerrada. Se bloquean los rangos especiales IANA que no son globalmente alcanzables; las banderas de `FILTER_FLAG_NO_PRIV_RANGE`/`FILTER_FLAG_NO_RES_RANGE` no bastan para este contrato y `FILTER_FLAG_GLOBAL_RANGE` no está disponible en el PHP mínimo soportado (8.1).
- La validación se ejecuta al crear/actualizar la subscription y de nuevo justo antes de cada delivery. En delivery se conecta a una IP del conjunto ya validado mediante pinning de resolución para evitar una segunda consulta DNS entre comprobación y conexión. No se confía en que la validación hecha al guardar siga vigente.
- No se siguen redirects. Cualquier 3xx es un fallo de delivery; no se solicita su `Location`, aunque apunte a otro host público. El transporte restringe los protocolos a HTTPS en producción y a HTTP/HTTPS fuera de producción; no usa proxies ambientales que puedan cambiar el destino efectivo.
- Las subscriptions HTTP existentes pueden continuar fuera de producción, pero requieren migrar el callback a HTTPS antes de entregar cuando `app_env` sea `prod` o `production`.
- Un rechazo por política no abre conexión HTTP y produce un resultado interno genérico, sin incluir secretos, query ni la URL completa en logs o errores al usuario. Los detalles de pinning, timeout, límites de respuesta y clasificación quedan para el paso 6; sus límites y fallos se cubrirán con fixtures, sin enviar requests a destinos reales en pruebas.

La política de “globalmente alcanzable” toma como fuente los registros especiales IANA IPv4 e IPv6: se bloquean las entradas marcadas como no globalmente alcanzables y se admiten las marcadas globales, además de direcciones unicast ordinarias que no estén en un rango especial bloqueado. El paso 6 debe implementar una tabla mantenible compatible con PHP 8.1 y rechazar por defecto direcciones malformadas o que no se puedan clasificar. Referencias: [registro especial IPv4 de IANA](https://www.iana.org/assignments/iana-ipv4-special-registry), [registro especial IPv6 de IANA](https://www.iana.org/assignments/iana-ipv6-special-registry), [flags de validación de PHP](https://www.php.net/manual/en/filter.constants.validation.php), [`CURLOPT_RESOLVE`](https://curl.se/libcurl/c/CURLOPT_RESOLVE.html) y [`CURLOPT_FOLLOWLOCATION`](https://curl.se/libcurl/c/CURLOPT_FOLLOWLOCATION.html).

## Secreto por subscription — paso 3 (2026-10-04)

- Se añadió `webhooks.secret` como columna `char(64) NOT NULL`; la migración genera 32 bytes aleatorios (representados como 64 caracteres hexadecimales) para cada subscription existente y admite reanudar un backfill incompleto. La migración no se ha ejecutado.
- Al crear una subscription, el servidor genera el secreto; la respuesta `POST /api/v1/webhooks` lo revela una sola vez. Para rotarlo se usa `PATCH /api/v1/webhooks/{id}/rotate_secret`; esa respuesta también lo revela una sola vez. Ambos caminos respetan los permisos de escritura y el propietario de la fila.
- Las lecturas CRUD ocultan `secret`; `POST` no acepta un secreto aportado por el cliente y `PUT`/`PATCH` no permiten editarlo. La única modificación es la rotación explícita.
- El adaptador de `ApiController` elimina recursivamente el secreto de los eventos de la entidad `webhooks`, y el matcher vuelve a filtrarlo de datos directos y filas previas de eventos `update`/`delete`/`list`. Por ello el secreto no forma parte de los envelopes de callback.
- La prueba focused `WebhookPublisherTest` verifica que no se filtren secretos de update ni secretos anidados en eventos `list`; no se usó base de datos ni callback real.

## Contrato de firma — paso 4

- Cada `WebhookEvent` tiene un `event_id` UUID v4, generado una vez al construir el evento o suministrado por quien lo publica. Se comparte entre todas sus subscriptions y se conserva en futuros reintentos.
- Cada intento añade `X-Simplerest-Webhook-Event-Id: <uuid>` y `X-Simplerest-Webhook-Timestamp: <unix-seconds-utc>`.
- El body es una única serialización JSON UTF-8 con `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR`; esos bytes se envían sin volver a codificarlos. `Content-Type` es `application/json; charset=utf-8`.
- El mensaje firmado es la concatenación exacta `v1.<timestamp>.<event_id>.<raw_body>`. Se calcula HMAC-SHA256 con el texto hexadecimal de 64 caracteres del secreto como clave UTF-8 (no se decodifica el hex) y digest lowercase hex. Header: `X-Simplerest-Webhook-Signature: v1=<digest>`.
- El timestamp y digest se generan por intento; el event ID se conserva entre intentos. El receptor firma/compara esos mismos bytes; el siguiente paso define la ventana temporal, comparación constante y deduplicación por event ID.

## Verificación del receptor — paso 5

- El consumidor verifica antes de parsear/normalizar el JSON: exige los tres headers, una versión conocida y el formato `v1=` seguido de 64 hexadecimales lowercase.
- `X-Simplerest-Webhook-Timestamp` debe contener segundos Unix enteros y quedar dentro de ±300 segundos respecto al reloj UTC del receptor. Fuera de ventana, la entrega se rechaza.
- Con el secreto UTF-8 exacto, calcula `hash_hmac('sha256', 'v1.' . $timestamp . '.' . $eventId . '.' . $rawBody, $secret)` y compara el digest calculado con el de `v1=` usando comparación constante (`hash_equals($expected, $received)`). El orden de argumentos es deliberado: el esperado local va primero y el valor del header va segundo.
- Tras validar la firma, el receptor registra atómicamente la pareja de su subscription local y `event_id` antes de aplicar efectos. Una pareja ya vista no vuelve a procesarse. Retener IDs al menos el doble de la ventana temporal evita replay dentro de esa ventana; la retención de idempotencia para retries debe cubrir el horizonte que defina la tarjeta de delivery asíncrono.
- El event ID del emisor es compartido entre subscriptions del mismo evento; por eso la clave de deduplicación incluye la subscription local, no solo el event ID global.

## Transporte y pruebas de seguridad — pasos 6 y 7 (2026-10-04)

- `WebhookEndpointPolicy` aplica la regla HTTPS cuando `app_env` es `prod` o `production`; en otros entornos admite HTTP y HTTPS. Valida al crear y actualizar callbacks y vuelve a validar al entregar.
- Para hostnames, valida todos los A/AAAA luego de seguir CNAME y rechaza resolución vacía, fallida o mixta con direcciones no globales. Los rangos especiales se mantienen como listas conservadoras basadas en IANA; ciertos bloques contenedores se rechazan completos aunque tengan excepciones globales más específicas.
- El transporte fija la IP validada mediante `CURLOPT_RESOLVE`, no usa proxy ambiental, no sigue redirects, restringe los protocolos al esquema validado y verifica certificado y nombre TLS. Requiere libcurl 7.21.3 o posterior; en una versión inferior falla cerrado.
- Límites: connect timeout 5 s, timeout total 10 s, request body 1 MiB, response body 64 KiB y headers 16 KiB. Los errores de transporte devuelven códigos genéricos y status HTTP opcional, sin URL, query ni mensaje cURL.
- El dispatcher serializa el JSON una vez y firma esos bytes con HMAC-SHA256; subscriptions sin secreto válido no se envían.
- Verificación ejecutada: `php vendor/bin/phpunit --no-coverage unit-tests/webhooks/WebhookPublisherTest.php unit-tests/webhooks/WebhookEndpointPolicyTest.php` — 14 tests, 71 assertions. También pasó `php -l` para los archivos tocados. Las pruebas usan fixtures DNS y opciones cURL inspeccionadas; no hicieron resolución DNS externa ni requests a callbacks.

Referencias para la clasificación de direcciones: [registro especial IPv4 IANA](https://www.iana.org/assignments/iana-ipv4-special-registry), [registro especial IPv6 IANA](https://www.iana.org/assignments/iana-ipv6-special-registry), [CURLOPT_RESOLVE](https://curl.se/libcurl/c/CURLOPT_RESOLVE.html).

## Documentación pública — paso 8 (2026-10-04)

- `docs/api/webhooks.md` documenta callback permitido por entorno, el filtrado DNS/IP y pinning, creación/rotación del secreto de un solo uso, headers y verificación PHP del cuerpo crudo, ventana temporal y deduplicación.
- Se corrigió la descripción del transporte heredada de la auditoría anterior: TLS se verifica y la entrega actual continúa siendo síncrona, sin retries automáticos.
- No se ejecutó la migración ni se hizo una entrega respaldada por base de datos o callback HTTP real.
