---
title: "Webhooks: desacoplar publicación de ApiController"
current_step: 1
next_step: 2
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

## Criterios de aceptación

- La lógica central de selección/publicación ya no reside en `ApiController`.
- El framework puede publicar un webhook sin ejecutar una acción REST.
- Los filtros actuales siguen funcionando.
- Los eventos CRUD existentes mantienen compatibilidad salvo cambio explícitamente documentado.
- El transporte HTTP queda detrás de una interfaz reemplazable y testeable.
