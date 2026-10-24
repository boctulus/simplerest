# Publicación de webhooks

## Alcance y recorrido

Los webhooks de SimpleRest son entregas HTTP salientes: una suscripción relaciona una entidad y una operación con una URL de callback. Se pueden producir desde las operaciones CRUD de `ApiController` o desde código interno mediante `WebhookPublisher`.

El recorrido es `caller → WebhookEvent → WebhookSubscriptionMatcher → WebhookDeliveryDispatcher → IWebhookTransport → callback`. El matcher selecciona suscripciones por entidad y evento, aplica condiciones y scope, y el dispatcher crea un payload firmado para cada callback.

## Gestionar suscripciones

La API de recursos expone la colección en `/api/v1/webhooks`. Sus campos configurables son:

| Campo | Contrato |
| --- | --- |
| `name` | Nombre opcional, hasta 50 caracteres. |
| `entity` | Requerido, hasta 50 caracteres; identifica la entidad o tabla del evento. |
| `op` | Requerido, hasta 255 caracteres; puede ser una operación CRUD o un nombre propio, por ejemplo `payment.refunded`. |
| `conditions` | Opcional, hasta 1024 caracteres; cadena de consulta interpretada por el matcher. |
| `callback` | Requerido, hasta 255 caracteres; debe cumplir la política de destinos indicada más abajo. |
| `belongs_to` | Owner de la suscripción. El controlador lo asigna al usuario autenticado al crearla; no equivale al alias de conexión. |
| `secret` | Lo genera el servidor. No lo envíes en el alta ni lo modifiques con CRUD. |

Ejemplo de alta:

```http
POST /api/v1/webhooks
Content-Type: application/json

{
  "name": "Avisos de reembolso",
  "entity": "payments",
  "op": "payment.refunded",
  "conditions": "status=refunded",
  "callback": "https://hooks.example.org/simplerest"
}
```

| Operación | Resultado |
| --- | --- |
| `GET /api/v1/webhooks` y `GET /api/v1/webhooks/{id}` | Lista o muestra suscripciones; ambas respuestas omiten `secret`. |
| `POST /api/v1/webhooks` | Crea una suscripción y devuelve el secreto una sola vez en la respuesta `201`. |
| `PUT /api/v1/webhooks/{id}` y `PATCH /api/v1/webhooks/{id}` | Actualiza la suscripción; no admite cambiar `secret`. |
| `PATCH /api/v1/webhooks/{id}/rotate_secret` | Reemplaza el secreto y devuelve el nuevo una sola vez. |
| `DELETE /api/v1/webhooks/{id}` | Elimina lógicamente la suscripción. |

Estas operaciones pasan por los controles ACL y de propietario de `ApiController`.

Para nombres de operación de más de 10 caracteres, aplica `2026_10_04_230000000_expand_webhook_op.php` cuando la tabla existente aún use el ancho original. Para persistir secretos, aplica `2026_10_04_233000000_add_webhook_delivery_secret.php`; esta migración agrega la columna y genera secretos para las filas anteriores. Crear la suscripción requiere que la columna ya exista.

## API independiente

El código interno puede publicar un evento sin ejecutar una acción REST ni depender de `auth()` o `request()`:

```php
use Boctulus\Simplerest\Core\Libs\WebhookEvent;
use Boctulus\Simplerest\Core\Libs\WebhookDeliveryDispatcher;
use Boctulus\Simplerest\Core\Libs\WebhookPublisher;
use Boctulus\Simplerest\Core\Libs\DB;

$scopeContext = [
    'connection_id' => DB::getCurrentConnectionId(true),
    'owner_scope' => 'single',
    'owner_field' => 'belongs_to', // usa el campo de ownership real de la entidad
    'owner_id' => $paymentOwnerId,
];

$event = new WebhookEvent(
    'payment.refunded',
    'payments',
    ['status' => 'refunded', 'amount' => 125],
    $paymentId,
    $actorId, // null cuando el evento no tiene actor
    $scopeContext
);

(new WebhookPublisher())->publish($event);
```

`WebhookEvent` recibe tipo de evento, entidad, datos, id opcional, actor nullable y contexto explícitos. `WebhookPublisher::publish()` coordina matching y entrega. El tipo puede ser un nombre propio como `payment.refunded`; para guardarlo en `webhooks.op` hay que aplicar antes la migración `2026_10_04_230000000_expand_webhook_op.php` cuando la columna aún tenga su ancho original.

En una entidad con owner, `scopeContext` debe declarar `connection_id`, `owner_scope: single`, el `owner_field` real y `owner_id`. Para una entidad sin owner, declara `owner_scope: global`; para un evento `list`, declara `owner_scope: rows` y el campo debe estar presente en cada fila. Un contexto ausente, inválido o sin owner resuelto no produce coincidencias. `tenant_id` no es una clave de scope reconocida: la conexión y el owner son fronteras distintas.

El contexto es metadato interno de confianza. `WebhookPublisher` no comprueba por sí mismo que el caller esté autorizado para publicar por una conexión u owner; deriva esos valores después de aplicar la autorización del caller y no uses `actorId` como sustituto de `owner_id`.

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

`webhook_id` identifica la suscripción; `event_type` y `entity` corresponden a `op` y `entity`; `id` es el id del registro de esa entidad; `user_id` es el actor y puede ser null. `at` es la hora del evento. El `event_id` no está en el JSON: viaja en el header de firma y es común a las suscripciones que reciben el mismo `WebhookEvent`.

`ApiController` publica `show`, `list`, `create`, `update` y `delete` después de la operación correspondiente. `Files` conserva los hooks por herencia; su evento `create` transmite los datos de `$_POST` y su evento `delete` usa un array vacío. En los demás casos, `data` contiene el payload de la operación, con enriquecimiento desde la fila persistida cuando aplica.

### Condiciones y datos de operación

`conditions` se interpreta con `parse_str()` como una cadena de consulta. `status=paid` comprueba igualdad; `status=paid,refunded` acepta cualquiera de esos valores; `status=null` comprueba un valor nulo. También admite operadores entre corchetes: `amount[gteq]=100`, `amount[between]=100,200` o `status[in]=paid,refunded`. Los operadores disponibles son `eq`, `neq`, `gt`, `lt`, `gteq`, `lteq`, `between`, `notBetween`, `in`, `notIn`, `contains`, `notContains`, `containsWord`, `notContainsWord`, `startsWith`, `notStartsWith`, `endsWith` y `notEndsWith`.

Las condiciones no seleccionan filas de `list`: una suscripción `list` con `conditions` no se entrega. En `update`, las condiciones se evalúan sobre los datos disponibles del evento antes de mezclar la fila que el matcher consulta. Si falta un campo de condición, la suscripción no coincide aunque ese campo exista en la fila almacenada. El objeto de datos se comparte entre suscripciones; una coincidencia anterior puede enriquecerlo y cambiar qué campos están disponibles para una suscripción posterior. Este orden legacy está cubierto por una regresión.

### Scope de conexión y propietario

El scope ya se aplica en el matcher. `connection_id` selecciona la conexión activa para leer suscripciones y datos de la entidad, y se restaura la conexión previa al terminar. Es un alias configurado de conexión; no es una columna `tenant_id`. Si un publisher interno omite `connection_id`, el matcher solo puede usar la conexión predeterminada cuando no hay una conexión no predeterminada activa. Conviene incluirla siempre.

`owner_scope` también es explícito y no se deriva de `actorId`:

- `single` requiere `owner_field` y `owner_id`; solo coincide con suscripciones cuyo `belongs_to` sea el mismo propietario. Un owner null explícito solo coincide con suscripciones cuyo `belongs_to` también sea null.
- `rows` solo es válido para `list`; cada fila debe incluir `owner_field` y cada suscripción recibe únicamente las filas que coinciden con su `belongs_to`.
- `global` se usa para entidades sin campo owner y permite coincidencias de `op` y `entity` dentro de la misma conexión.

`ApiController` construye este contexto después de aplicar sus controles de ACL. En operaciones singulares deriva el owner del payload o de la fila persistida; en `list` solicita el filtrado por filas. Un owner sin resolver, un modo inválido, una conexión desconocida o un `list` sin el campo owner requerido no produce entregas. El publisher interno debe construir el contexto desde datos confiables y después de autorizar la operación; `WebhookPublisher` no autoriza por sí mismo al actor.

Las pruebas focalizadas usan fixtures que modelan conexiones y owners distintos, particionan filas de `list` y comprueban el fallo cerrado. No se ha ejecutado una lectura de suscripciones ni una entrega con una base de datos real.

## Callbacks y secretos

Antes de crear webhooks, aplica la migración `2026_10_04_233000000_add_webhook_delivery_secret.php`. El servidor genera un secreto independiente de 32 bytes aleatorios, representado por 64 caracteres hexadecimales. La respuesta `201` de `POST /api/v1/webhooks` incluye el secreto una sola vez dentro del registro creado. Guárdalo al recibirlo: las lecturas `show` y `list` lo ocultan.

Para reemplazarlo, usa `PATCH /api/v1/webhooks/{id}/rotate_secret`. La respuesta incluye el secreto nuevo una sola vez; desde ese momento se rechazan firmas hechas con el anterior. El alta no acepta un secreto del cliente y `PUT`/`PATCH` CRUD no permiten cambiarlo directamente.

Los callbacks deben ser URLs absolutas `http://` o `https://`, sin credenciales en la URL ni fragmento. Solo se admiten destinos globalmente alcanzables. Se rechazan las direcciones privadas, loopback, link-local, CGNAT, multicast y los bloques especiales no globales; algunos bloques contenedores se rechazan completos aunque incluyan excepciones globales más específicas. Para un hostname se comprueban todas las respuestas A/AAAA después de seguir CNAME; cualquier respuesta no global, una respuesta vacía o un error de DNS hace fallar la operación. La regla se ejecuta al crear o actualizar y otra vez justo antes de entregar; la IP validada se fija para la conexión.

La clasificación usa los [registros especiales IPv4](https://www.iana.org/assignments/iana-ipv4-special-registry) y [IPv6](https://www.iana.org/assignments/iana-ipv6-special-registry) de IANA.

Cuando la configuración `app_env` resuelve a `prod` o `production`, solo se acepta HTTPS. En otros entornos también puede usarse HTTP, pero el transporte sigue exigiendo una dirección pública y no permite redirects. HTTP transmite el payload sin cifrar; reserva esos callbacks para pruebas sin datos sensibles. Las conexiones HTTPS siempre verifican certificado y nombre del host. Las subscriptions con HTTP deben migrar a HTTPS antes de activar un entorno de producción.

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

El publisher realiza la entrega de forma síncrona mediante `WebhookHttpTransport`. El transporte deshabilita redirects y proxies, verifica TLS, limita connect timeout a 5 s, timeout total a 10 s, request body a 1 MiB, response body a 64 KiB y headers a 16 KiB. El pinning DNS requiere libcurl 7.21.3 o posterior; las versiones anteriores fallan cerradas. Antes de libcurl 7.57.0 el transporte solo selecciona direcciones IPv4 para pinning, por lo que un destino disponible únicamente por IPv6 falla cerrado. Los errores internos omiten URL, query y mensaje crudo de cURL.

Las suscripciones se entregan en secuencia dentro del mismo flujo que publica el evento. Por eso la latencia puede crecer con el número de callbacks; cada petición puede esperar hasta 10 s. No hay cola, reintentos automáticos, registro persistido por intento ni replay manual en esta ruta. Aunque el transporte produce un resultado interno, el dispatcher no lo expone ni lo guarda.

Las pruebas focalizadas cubren matching, scope de conexión/owner con fixtures, condiciones legacy de `update`, firma, política IP/DNS y opciones de transporte con fixtures. No se ha ejecutado una lectura de suscripciones respaldada por base de datos ni una llamada HTTP a un callback real.

## Diferencia respecto de EventBus

`EventBus` es un observer en memoria para componentes del mismo proceso: `attach()` y `detach()` registran observadores y `notify($data)` los invoca en secuencia. Además, el trait guarda solo el último valor notificado mediante la caché configurada, bajo `eventbus_last_event`, con TTL de siete días. No consulta la tabla `webhooks`, no envía callbacks HTTP ni firma entregas. Usa `WebhookPublisher` para integraciones HTTP externas.
