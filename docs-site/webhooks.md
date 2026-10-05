---
title: Webhooks
source:
  - docs/api/webhooks.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

# Webhooks

El publisher puede emitir eventos desde código interno sin ejecutar una acción REST ni depender de `auth()` o `request()`:

```php
use Boctulus\Simplerest\Core\Libs\WebhookEvent;
use Boctulus\Simplerest\Core\Libs\WebhookPublisher;

$event = new WebhookEvent(
    'payment.refunded',
    'payments',
    ['status' => 'refunded', 'amount' => 125],
    $paymentId,
    $actorId,
    ['tenant_id' => $tenantId]
);

(new WebhookPublisher())->publish($event);
```

`WebhookPublisher` coordina el matching de subscriptions y la entrega. Se pueden inyectar matcher y dispatcher; un transporte alternativo implementa `IWebhookTransport`. `ApiController::webhook()` conserva compatibilidad CRUD para `show`, `list`, `create`, `update` y `delete`. El envelope de callback contiene `webhook_id`, `event_type`, `entity`, `id`, `data`, `user_id` y `at`.

## Condiciones de `update`

El matcher selecciona subscriptions por `op` y `entity`; sus condiciones se almacenan como query string. Para un `update`, exige los campos de condición en los datos compartidos disponibles antes de combinar la fila existente. El objeto se comparte entre iteraciones, así que una subscription previa puede enriquecerlo y hacer que el resultado dependa del orden. Una condición sobre un campo ausente no coincide por el valor que tenga ese campo en la fila almacenada.

`scopeContext` llega al evento, pero el matcher actual no lo usa para aislar subscriptions. No es una frontera de seguridad ni acredita aislamiento por tenant.

## Secretos y firma

Antes de crear webhooks se requieren las migraciones `2026_10_04_230000000_expand_webhook_op.php` para tipos de evento personalizados y `2026_10_04_233000000_add_webhook_delivery_secret.php` para el secreto de entrega.

El servidor genera un secreto aleatorio independiente de 32 bytes, expresado en 64 caracteres hexadecimales. `POST /api/v1/webhooks` lo devuelve una sola vez; `show` y `list` lo ocultan. `PATCH /api/v1/webhooks/{id}/rotate_secret` lo reemplaza y desde entonces firmas con el anterior dejan de ser válidas.

Cada entrega incluye `X-Simplerest-Webhook-Event-Id`, `X-Simplerest-Webhook-Timestamp` y `X-Simplerest-Webhook-Signature: v1=<hex>`. El ID se comparte entre subscriptions del mismo evento; timestamp y firma se calculan por intento. La firma HMAC-SHA256 cubre exactamente `v1.<timestamp>.<event_id>.<raw_body>` con el secreto usado como texto UTF-8. Verifica el cuerpo crudo antes de parsear JSON y rechaza timestamps fuera de ±300 segundos. El receptor debe deduplicar atómicamente la pareja de event ID e ID local de subscription antes de aplicar efectos, y conservar esos IDs al menos 10 minutos; si más adelante se habilitan reintentos, la retención debe cubrir su horizonte.

## Destinos y entrega

Los callbacks deben ser URLs absolutas HTTP o HTTPS, sin credenciales ni fragmento, con destino globalmente alcanzable. Se rechazan direcciones privadas, loopback, link-local, CGNAT, multicast y bloques especiales no globales. Se comprueban todas las respuestas DNS A/AAAA después de seguir CNAME; una dirección no global, respuesta vacía o error DNS hace fallar la operación. La regla corre al crear o actualizar y justo antes de enviar; la IP se fija para la conexión.

En `prod`/`production` sólo se admite HTTPS. El transporte verifica TLS, no sigue redirects ni proxies y limita connect timeout a 5 s, timeout total a 10 s, body de request a 1 MiB, body de respuesta a 64 KiB y headers a 16 KiB. El pinning DNS requiere libcurl 7.21.3 o posterior; versiones anteriores fallan cerradas. HTTP transmite el payload sin cifrar y queda reservado a pruebas sin datos sensibles.

La entrega actual es síncrona: no hay cola ni reintentos automáticos, y el dispatcher no expone el resultado del transporte. Los errores internos omiten URL, query y mensaje crudo de cURL. Hay pruebas focalizadas con fixtures para matching, firma, política IP/DNS y opciones de transporte; no se ejecutó entrega con base de datos ni llamada HTTP a un callback real.
