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

## Entrega actual y límites

El publisher despacha las entregas de forma síncrona a través de `WebhookHttpTransport`, que conserva el transporte existente basado en `consume_api()`. No hay cola, retries, replay ni idempotencia en esta ruta; el dispatcher también ignora el resultado del transporte. La verificación SSL de callbacks sigue deshabilitada en el transporte actual y corresponde resolverla en el trabajo de seguridad de callbacks.

Las pruebas focalizadas verifican el matching con fixtures en memoria, el orden de publicación, el envelope y el transporte sustituible. No se ha reproducido aquí una entrega con base de datos y callback HTTP real.
