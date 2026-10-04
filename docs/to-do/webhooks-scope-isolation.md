---
title: "Webhooks: aislamiento de tenant y ownership"
current_step: 1
next_step: 2
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
tags: [webhooks, security, multitenancy, ownership]
---

## Objetivo

Eliminar cualquier posibilidad de que una suscripción de webhook reciba eventos fuera de su scope. La tabla actual contiene `belongs_to`, pero el dispatcher debe demostrar y aplicar explícitamente el aislamiento correcto para cada modo de tenancy soportado por SimpleRest.

## Problema actual

El lookup del dispatcher está centrado en `op + entity`. Debe auditarse cómo intervienen `belongs_to`, `tenantid`, la conexión activa y el usuario autenticado antes de asumir que la suscripción queda aislada por tenant o propietario.

Este trabajo es de seguridad: no debe resolverse suponiendo que `belongs_to` equivale siempre a `tenant_id`.

## Pasos planificados

1. **Auditar el scope real** — documentar dónde vive `webhooks`, qué conexión usa el dispatcher, qué significa `belongs_to` en este recurso y cómo se comporta con tenancy por conexión/base de datos.
2. **Definir el contrato de scope** — establecer qué campos/contexto identifican tenant, owner y actor; decidir si hace falta un `tenant_id` explícito o si el aislamiento por conexión es suficiente.
3. **Aplicar el scope en todas las consultas** — impedir que el dispatcher seleccione subscriptions de otro tenant/owner y evitar bypasses desde publicación interna o impersonation.
4. **Endurecer CRUD de subscriptions** — asegurar que crear, listar, modificar y borrar webhooks respete el mismo aislamiento que la entrega.
5. **Agregar pruebas de no cruce** — cubrir al menos dos tenants/owners con misma `entity`, `op` y callback distintos, verificando que ningún evento se entregue al scope incorrecto.
6. **Documentar la garantía** — dejar explícito el modelo de aislamiento y cualquier migración necesaria.

## Criterios de aceptación

- Ninguna selección de subscriptions depende únicamente de `entity + op` cuando existe scope aplicable.
- El código no interpreta implícitamente `belongs_to` como tenant sin contrato documentado.
- CRUD y dispatch usan reglas de scope coherentes.
- Existen pruebas automatizadas que fallen ante una entrega cross-tenant/cross-owner.
- Las migraciones son backward-compatible o incluyen una estrategia de transición explícita.
