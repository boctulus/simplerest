---
title: "Webhooks: desacoplar publicación de ApiController"
current_step: 2
next_step: 3
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
3. **Adaptar ApiController** — convertir los hooks CRUD actuales en un adaptador que publique mediante la nueva abstracción, preservando comportamiento y compatibilidad.
4. **Habilitar publicación interna** — permitir que servicios/jobs/comandos publiquen eventos directamente usando la misma ruta de filtros, scope y envelope.
5. **Separar selección de entrega** — dejar claramente separados: construcción del evento, selección de subscriptions y transporte HTTP. La entrega asíncrona se resolverá en una tarea específica.
6. **Agregar pruebas de regresión** — demostrar que los webhooks CRUD existentes siguen funcionando y que un evento publicado fuera de `ApiController` también dispara subscriptions compatibles.
7. **Actualizar documentación** — documentar la API nueva y marcar la llamada directa desde `ApiController` como detalle de compatibilidad, no como arquitectura normativa.

## Auditoría completada — paso 1 (2026-10-04)

- `ApiController::webhook()` es `protected`, valida solo `show`, `list`, `create`, `update` y `delete`, y recibe operación, datos e id opcional. Los hooks CRUD aparecen en `src/framework/Api/ApiController.php` para show, list, create, update y delete; `src/framework/Api/Files.php` también publica create y delete mediante herencia.
- La consulta de subscriptions llama a `DB::getDefaultConnection()` y filtra únicamente por `op` y `entity`. En `DB`, ese método selecciona la conexión configurada como default. El método no restaura después la conexión previa.
- El envelope actual contiene `webhook_id`, `event_type`, `entity`, `id`, `data`, `user_id` y `at`. `webhook_id` cambia por subscription; `id` identifica el registro de la entidad y no existe un id independiente del evento. La construcción también lee `auth()->uid()` y, para `show`, `request()->getQuery('fields')`.
- Las condiciones se parsean como query string y se evalúan con `Strings::filter()`. Para `list`, una subscription con condiciones no llega a enviarse. Para `update` con condiciones, el filtro exige que sus campos estén en los datos entrantes y se evalúa antes de mezclar la fila previa; en los demás casos filtrados se evalúan los datos del envelope, que para update/delete y show con `fields` incluyen una lectura y mezcla de la fila previa.
- El transporte es `consume_api($callback, 'POST', $body)` ejecutado dentro del loop de subscriptions antes de terminar la operación API; se ignora su resultado. `consume_api()` crea `ApiClient`, y `withoutStrictSSL()` desactiva la validación SSL del host y del certificado. No hay una dependencia de transporte inyectable en esta ruta.
- `Files::post()` pasa `$_POST` al evento create; `Files::delete()` pasa un array vacío al evento delete. Esos payloads difieren del registro persistido.
- La búsqueda literal de `webhook` en `unit-tests/` no encontró referencias. No se ejecutaron tests en este paso de auditoría.

Referencias: `ApiController.php` (llamadas CRUD 558, 1092, 1592, 2321, 2482; dispatcher 2528–2605), `Files.php` (122, 217), `Helpers/url.php` (consume_api), `Libs/DB.php` (245–247) y `Libs/ApiClient.php` (526–538).

## Criterios de aceptación

- La lógica central de selección/publicación ya no reside en `ApiController`.
- El framework puede publicar un webhook sin ejecutar una acción REST.
- Los filtros actuales siguen funcionando.
- Los eventos CRUD existentes mantienen compatibilidad salvo cambio explícitamente documentado.
- El transporte HTTP queda detrás de una interfaz reemplazable y testeable.
