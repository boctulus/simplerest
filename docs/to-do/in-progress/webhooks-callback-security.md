---
title: "Webhooks: seguridad de callbacks y firma HMAC"
current_step: 2
next_step: 3
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
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
