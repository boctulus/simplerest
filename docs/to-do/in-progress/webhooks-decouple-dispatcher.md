---
title: "Webhooks: desacoplar publicación de ApiController"
current_step: 4
next_step: 5
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
tags: [webhooks, architecture, events, api]
---

## Objetivo

Separar la semántica de webhooks del ciclo HTTP de `ApiController` para que SimpleRest pueda publicar eventos desde controllers, servicios, jobs, comandos y código interno sin duplicar lógica ni simular requests REST.

## Restricción de compatibilidad

SimpleRest es un framework genérico. Los eventos CRUD existentes (`show`, `list`, `create`, `update`, `delete`) no deben eliminarse automáticamente por ser poco apropiados para POSDriven. Deben conservarse como contrato legacy mientras se define una API de publicación más general.

## Pasos planificados

1. **Auditar el contrato actual** — relevar todos los puntos donde `ApiController` llama a `webhook()`, el envelope generado, el filtrado de `conditions` y las dependencias directas con `consume_api()`.
2. **Diseñar una API de publicación independiente** — introducir una abstracción de framework (`WebhookPublisher`, `WebhookDispatcher` o equivalente) que reciba evento, entidad, payload, id y contexto de scope sin depender del controller HTTP.
3. **Definir evento y matcher** — agregar el tipo de evento y el componente de matching por evento/entidad/condiciones, preservando literalmente el orden/semántica actual durante este refactor.
4. **Implementar dispatcher y transporte** — agregar el dispatcher de deliveries y un transporte HTTP detrás de una interfaz inyectable, sin mezclar persistencia/retries asíncronos de la tarjeta correspondiente.
5. **Publicar la API independiente** — conectar evento, matcher y dispatcher en `WebhookPublisher::publish()`, con actor y contexto explícitos y sin globals HTTP.
6. **Adaptar ApiController** — dejar `webhook()` como adaptador legacy CRUD que construye el evento y delega al publisher; conservar los hooks de `Files` por herencia.
7. **Habilitar eventos no CRUD** — ampliar `op` y su validación mediante migración compatible antes de publicar nombres de eventos que excedan el límite actual de 10 caracteres.
8. **Agregar pruebas de regresión** — demostrar compatibilidad CRUD, la semántica actual de condiciones de update y publicación interna con transportes sustituibles.
9. **Actualizar documentación** — documentar la API nueva y marcar la llamada desde `ApiController` como adaptador de compatibilidad.

## Diseño acordado — paso 2 (2026-10-04)

```text
ApiController (adaptador CRUD)
    ↓
WebhookEvent / WebhookPublisher
    ↓
WebhookSubscriptionMatcher
    ↓
WebhookDeliveryDispatcher
    ↓
IWebhookTransport → transporte HTTP
```

- `WebhookPublisher` será el punto de entrada usado por código HTTP e interno. Recibirá actor y `scopeContext` explícitos; no leerá `auth()`, `request()` ni `ApiController`.
- `WebhookSubscriptionMatcher` resolverá subscriptions y condiciones. El contrato de `scopeContext` se transportará sin equiparar `belongs_to` con tenant; su interpretación concreta queda para `webhooks-scope-isolation.md`.
- `WebhookDeliveryDispatcher` preparará una entrega por subscription y la pasará a `IWebhookTransport`. El matcher no realizará HTTP y el publisher no consultará subscriptions.
- El adaptador HTTP inicial envolverá el comportamiento existente de `consume_api()` para que el cambio arquitectónico preserve el contrato de entrega. La verificación SSL desactivada queda registrada para la tarea `webhooks-callback-security.md`.
- Se conservarán el envelope CRUD y el orden actual de filtrado. En particular, `update` con condiciones seguirá filtrando los datos entrantes antes de mezclar la fila; cualquier cambio semántico requerirá definición y regresión separadas.
- `EventBus` no se reutilizará: el código existente notifica observers en memoria y guarda el último evento en cache; no selecciona subscriptions persistidas ni realiza entregas HTTP.
- El campo `webhooks.op` es `VARCHAR(10)` y la validación también limita a 10. Antes de habilitar nombres de evento generales, el paso 7 lo ampliará a 255, preservando los valores CRUD existentes. No se añadirá un id estable de evento en este refactor; se definirá con outbox/idempotencia.

## Auditoría completada — paso 1 (2026-10-04)

- `ApiController::webhook()` es `protected`, valida solo `show`, `list`, `create`, `update` y `delete`, y recibe operación, datos e id opcional. Los hooks CRUD aparecen en `src/framework/Api/ApiController.php` para show, list, create, update y delete; `src/framework/Api/Files.php` también publica create y delete mediante herencia.
- La consulta de subscriptions llama a `DB::getDefaultConnection()` y filtra únicamente por `op` y `entity`. En `DB`, ese método selecciona la conexión configurada como default. El método no restaura después la conexión previa.
- El envelope actual contiene `webhook_id`, `event_type`, `entity`, `id`, `data`, `user_id` y `at`. `webhook_id` cambia por subscription; `id` identifica el registro de la entidad y no existe un id independiente del evento. La construcción también lee `auth()->uid()` y, para `show`, `request()->getQuery('fields')`.
- Las condiciones se parsean como query string y se evalúan con `Strings::filter()`. Para `list`, una subscription con condiciones no llega a enviarse. Para `update` con condiciones, el filtro exige que sus campos estén en los datos entrantes y se evalúa antes de mezclar la fila previa; en los demás casos filtrados se evalúan los datos del envelope, que para update/delete y show con `fields` incluyen una lectura y mezcla de la fila previa.
- El transporte es `consume_api($callback, 'POST', $body)` ejecutado dentro del loop de subscriptions antes de terminar la operación API; se ignora su resultado. `consume_api()` crea `ApiClient`, y `withoutStrictSSL()` desactiva la validación SSL del host y del certificado. No hay una dependencia de transporte inyectable en esta ruta.
- `Files::post()` pasa `$_POST` al evento create; `Files::delete()` pasa un array vacío al evento delete. Esos payloads difieren del registro persistido.
- La búsqueda literal de `webhook` en `unit-tests/` no encontró referencias. No se ejecutaron tests en este paso de auditoría.

Referencias: `ApiController.php` (llamadas CRUD 558, 1092, 1592, 2321, 2482; dispatcher 2528–2605), `Files.php` (122, 217), `Helpers/url.php` (consume_api), `Libs/DB.php` (245–247) y `Libs/ApiClient.php` (526–538).

## Implementación completada — paso 3 (2026-10-04)

- Se agregó `WebhookEvent` con tipo, entidad, datos, id, actor, contexto de scope, indicador explícito de `fields` solicitado y hora de creación. No consulta `auth()` ni `request()`.
- Se agregó `WebhookSubscriptionMatcher::findMatches()` para seleccionar por `op` + `entity` y reproducir el filtrado y enriquecimiento actuales antes de entregar resultados preparados para el dispatcher.
- Para `update` con condiciones, el matcher evalúa las condiciones sobre los datos entrantes antes de mezclar la fila actual, igual que el código anterior. No se cambió esa semántica.
- El contexto de scope ya forma parte del evento, pero aún no altera la consulta; el contrato de aislamiento continúa en la tarea correspondiente.
- Verificación estática: `php -l` pasó en ambos archivos nuevos. El publisher, el dispatcher, el transporte y el adaptador HTTP aún corresponden a pasos posteriores.

## Criterios de aceptación

- La lógica central de selección/publicación ya no reside en `ApiController`.
- El framework puede publicar un webhook sin ejecutar una acción REST.
- Los filtros actuales siguen funcionando.
- Los eventos CRUD existentes mantienen compatibilidad salvo cambio explícitamente documentado.
- El transporte HTTP queda detrás de una interfaz reemplazable y testeable.
