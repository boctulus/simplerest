---
title: "Webhooks: entrega asíncrona, retries y replay"
current_step: 1
next_step: 2
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
tags: [webhooks, reliability, outbox, retries, observability]
---

## Objetivo

Evitar que la latencia o caída de un callback externo bloquee una operación de SimpleRest y dotar al motor de trazabilidad, reintentos controlados y replay.

## Problema actual

La entrega termina ejecutando el callback HTTP directamente mediante `consume_api()` dentro del flujo que procesa la operación. No hay un contrato general de delivery persistido con estado, intentos, backoff, replay ni dead-letter.

## Pasos planificados

1. **Auditar capacidades existentes** — comprobar si el framework ya dispone de queue/jobs/scheduler reutilizable y definir la estrategia mínima sin introducir una dependencia externa obligatoria.
2. **Definir persistencia de eventos y deliveries** — diseñar almacenamiento equivalente a `webhook_events` y `webhook_deliveries`, separando un evento canónico de sus entregas por subscription.
3. **Implementar outbox transaccional cuando aplique** — persistir el evento antes de entregar y desacoplar la respuesta de negocio del POST al callback.
4. **Implementar worker de delivery** — procesar pendientes con timeout explícito, captura de status/error, timestamps e identificadores estables.
5. **Agregar retry/backoff** — definir política para errores de red, 5xx y respuestas no retryable; limitar intentos y soportar estado dead-letter/final.
6. **Agregar replay manual seguro** — permitir reintentar una delivery o regenerar deliveries desde un evento persistido sin recrear el evento de negocio.
7. **Agregar observabilidad** — registrar attempt, HTTP status, último error, siguiente intento, delivered_at y duración sin almacenar secretos en logs.
8. **Cubrir compatibilidad y fallos** — probar callback lento, timeout, 500, 4xx, caída del worker, recuperación posterior y múltiples subscriptions para un mismo evento.

## Criterios de aceptación

- Una operación de negocio no espera al servidor remoto para completar su respuesta normal.
- Cada evento tiene identidad estable y cada subscription genera una delivery independiente.
- Los intentos y errores quedan persistidos y consultables.
- Existe retry con backoff y límite explícito.
- Existe replay sin volver a ejecutar la operación de negocio original.
- La solución funciona con infraestructura incluida en SimpleRest o documenta claramente cualquier requisito adicional.
