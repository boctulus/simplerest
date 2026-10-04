# Publicación de webhooks

## API independiente

El código interno puede publicar un evento sin ejecutar una acción REST ni depender de `auth()` o `request()`:

```php
use Boctulus\Simplerest\Core\Libs\WebhookEvent;
use Boctulus\Simplerest\Core\Libs\WebhookDeliveryDispatcher;
use Boctulus\Simplerest\Core\Libs\WebhookPublisher;

$event = new WebhookEvent(
    'payment.refunded',
    'payments',
    ['status' => 'refunded', 'amount' => 125],
    $paymentId,
    $actorId, // null cuando el evento no tiene actor
    ['tenant_id' => $tenantId]
);

(new WebhookPublisher())->publish($event);
```

`WebhookEvent` recibe tipo de evento, entidad, datos, id opcional, actor nullable y contexto explícitos. `WebhookPublisher::publish()` coordina matching y entrega. El tipo puede ser un nombre propio como `payment.refunded`; para guardarlo en `webhooks.op` hay que aplicar antes la migración `2026_10_04_230000000_expand_webhook_op.php`.

El flujo tiene estas fronteras:

```text
caller
  → WebhookEvent / WebhookPublisher
  → WebhookSubscriptionMatcher
  → WebhookDeliveryDispatcher
  → IWebhookTransport
```

El constructor de `WebhookPublisher` permite inyectar matcher y dispatcher. Para sustituir el HTTP se implementa `IWebhookTransport` y se entrega al dispatcher:

```php
$dispatcher = new WebhookDeliveryDispatcher($transport);
$publisher = new WebhookPublisher(deliveryDispatcher: $dispatcher);
$publisher->publish($event);
```

## Compatibilidad CRUD

`ApiController::webhook()` es el adaptador de compatibilidad para `show`, `list`, `create`, `update` y `delete`: construye el evento con el actor y el contexto disponibles y delega al publisher. Los hooks de `Files` se mantienen por herencia. El envelope de callback conserva estas claves:

```text
webhook_id, event_type, entity, id, data, user_id, at
```

El matcher selecciona subscriptions por `op` y `entity`. Las condiciones actuales se almacenan como query string. Para cada `update` condicionado, exige los campos de condición en los datos compartidos disponibles y los evalúa antes de que esa subscription mezcle la fila existente. El objeto de datos se comparte entre iteraciones, así que una subscription anterior puede haberlo enriquecido; el resultado puede depender del orden. Sin ese enriquecimiento previo, una condición sobre un campo ausente del update no coincide aunque el valor aparezca en la fila existente. Esta semántica legacy se conserva deliberadamente y está cubierta por regresiones.

`scopeContext` queda disponible en el evento, pero el matcher actual todavía no lo usa para aislar subscriptions. No debe tratarse como una frontera de seguridad hasta completar el aislamiento de tenant.

## Callbacks y secretos

Antes de crear webhooks, aplica la migración `2026_10_04_233000000_add_webhook_delivery_secret.php`. El servidor genera un secreto independiente de 32 bytes aleatorios, representado por 64 caracteres hexadecimales. La respuesta `201` de `POST /api/v1/webhooks` incluye el secreto una sola vez dentro del registro creado. Guárdalo al recibirlo: las lecturas `show` y `list` lo ocultan.

Para reemplazarlo, usa `PATCH /api/v1/webhooks/{id}/rotate_secret`. La respuesta incluye el secreto nuevo una sola vez; desde ese momento se rechazan firmas hechas con el anterior. El alta no acepta un secreto del cliente y `PUT`/`PATCH` CRUD no permiten cambiarlo directamente.

Los callbacks deben ser URLs absolutas `http://` o `https://`, sin credenciales en la URL ni fragmento. Solo se admiten destinos globalmente alcanzables. Se rechazan las direcciones privadas, loopback, link-local, CGNAT, multicast y los bloques especiales no globales; algunos bloques contenedores se rechazan completos aunque incluyan excepciones globales más específicas. Para un hostname se comprueban todas las respuestas A/AAAA después de seguir CNAME; cualquier respuesta no global, una respuesta vacía o un error de DNS hace fallar la operación. La regla se ejecuta al crear o actualizar y otra vez justo antes de entregar; la IP validada se fija para la conexión.

La clasificación usa los [registros especiales IPv4](https://www.iana.org/assignments/iana-ipv4-special-registry) y [IPv6](https://www.iana.org/assignments/iana-ipv6-special-registry) de IANA.

Cuando `APP_ENV` resuelve a `prod` o `production`, solo se acepta HTTPS. En otros entornos también puede usarse HTTP, pero el transporte sigue exigiendo una dirección pública y no permite redirects. HTTP transmite el payload sin cifrar; reserva esos callbacks para pruebas sin datos sensibles. Las conexiones HTTPS siempre verifican certificado y nombre del host. Las subscriptions con HTTP deben migrar a HTTPS antes de activar un entorno de producción.

## Verificar la firma

Cada entrega incluye estos headers:

```text
X-Simplerest-Webhook-Event-Id: <uuid-v4>
X-Simplerest-Webhook-Timestamp: <segundos Unix>
X-Simplerest-Webhook-Signature: v1=<64 hex lowercase>
Content-Type: application/json; charset=utf-8
```

El `event_id` se comparte entre las subscriptions de un mismo evento. El timestamp y la firma se calculan por intento. La firma usa el secreto como texto UTF-8 (no se decodifica el hexadecimal) y cubre exactamente `v1.<timestamp>.<event_id>.<raw_body>`. Verifica el cuerpo crudo antes de parsear o normalizar el JSON. Este ejemplo recibe los headers ya normalizados por el adaptador HTTP:

```php
function verifyWebhook(string $rawBody, array $headers, string $secret): ?array
{
    $eventId = $headers['X-Simplerest-Webhook-Event-Id'] ?? '';
    $timestamp = $headers['X-Simplerest-Webhook-Timestamp'] ?? '';
    $signature = $headers['X-Simplerest-Webhook-Signature'] ?? '';

    if (
        !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $eventId)
        || !ctype_digit($timestamp)
        || !preg_match('/^v1=([0-9a-f]{64})$/', $signature, $matches)
        || !preg_match('/^[0-9a-f]{64}$/', $secret)
    ) {
        return null;
    }

    $sentAt = filter_var($timestamp, FILTER_VALIDATE_INT);
    if ($sentAt === false || abs(time() - $sentAt) > 300) {
        return null;
    }

    $expected = hash_hmac(
        'sha256',
        'v1.' . $timestamp . '.' . $eventId . '.' . $rawBody,
        $secret
    );

    if (!hash_equals($expected, $matches[1])) {
        return null;
    }

    return json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
}
```

Rechaza firmas inválidas o timestamps fuera de ±300 segundos. Después de verificar, registra atómicamente el `event_id` junto con el id de tu subscription local antes de aplicar efectos; si esa pareja ya existe, reconoce el duplicado sin volver a procesarlo. Mantén esos IDs al menos 10 minutos y amplía la retención para cubrir el horizonte de reintentos si se habilitan retries en el futuro.

## Entrega actual y límites

El publisher realiza la entrega de forma síncrona mediante `WebhookHttpTransport`. El transporte deshabilita redirects y proxies, verifica TLS, limita connect timeout a 5 s, timeout total a 10 s, request body a 1 MiB, response body a 64 KiB y headers a 16 KiB. El pinning DNS requiere libcurl 7.21.3 o posterior; las versiones anteriores fallan cerradas. Los errores internos omiten URL, query y mensaje crudo de cURL.

No hay cola ni reintentos automáticos en esta ruta y el dispatcher no expone el resultado del transporte. Las pruebas focalizadas cubren matching, condiciones legacy de `update`, firma, política IP/DNS y opciones de transporte con fixtures. No se ha ejecutado una entrega con base de datos ni una llamada HTTP a un callback real.
