---
title: "Webhooks: aislamiento de tenant y ownership"
current_step: 3
next_step: 3
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

El lookup del matcher está centrado en `op + entity`, selecciona la conexión `main` explícitamente y no aplica filtro de propietario. Debe definirse por separado cómo intervienen la conexión seleccionada y `belongs_to`; no son el mismo scope.

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

## Auditoría de scope real — paso 1 (2026-10-04)

- `Request::getTenantId()` lee `tenantid` de query string o del header `X-TENANT-ID`. `ApiController::__construct()` pasa ese valor a `DB::getConnection($tenantid)`; en este framework es un alias de conexión configurado, que puede seleccionar base y/o prefijo. No se persiste como una columna `tenant_id` en `webhooks`.
- La configuración del checkout tiene varias conexiones, `main` como default y `restrict_by_tenant = false`. `AuthController` solo compara la conexión solicitada con `db_access` cuando esa restricción está activada; esta tarjeta no cambiará la política general de acceso a conexiones.
- `WebhooksSchema` tiene `belongs_to`, sin `tenant_id`. `QueryBuilderTrait::belongsTo()` lo define como campo de ownership predeterminado. `ApiController` usa ese campo para acotar show/list y comprobar writes/deletes salvo permisos globales; al crear suele asignarlo al usuario autenticado. Por tanto, propietario de fila y alias de conexión son scopes distintos. `actorId` identifica al actor que originó la petición y no sustituye el propietario del recurso.
- `ApiController::webhook()` incluye en `scopeContext` el valor solicitado como `tenant_id` solo cuando no es null. El matcher no lee ese contexto: `loadSubscriptions()` llama a `DB::getDefaultConnection()` y filtra únicamente por `op + entity`. Después, `loadEntityData()` consulta la entidad en esa conexión default. Una operación atendida en una conexión no default puede, por ello, consultar subscriptions y datos previos de `main`.
- En una misma conexión, el matcher tampoco filtra las subscriptions por `belongs_to`; un evento con la misma entidad y operación puede incluir subscriptions de otros propietarios. CRUD aplica checks de ownership en `ApiController`, pero ese scope no se transmite al matcher.
- El publisher interno permite contexto explícito, pero no valida por sí mismo que el actor pueda publicar para un owner o una conexión. La definición del paso 2 debe separar conexión de tenant/owner, identificar cómo se deriva el owner de cada evento y fijar el comportamiento fail-closed si falta ese dato.
- Auditoría estática de `Request`, `ApiController`, `DB`, `DBRels`, `WebhookEvent`, `WebhookSubscriptionMatcher`, `WebhooksSchema` y `AuthController`. No se abrieron conexiones ni se leyeron datos de base de datos.

## Contrato de scope — paso 2 (2026-10-04)

- El aislamiento de tenant usa el alias de conexión activo, no una columna `tenant_id`. El contexto se llama `connection_id` porque `tenantid` selecciona una entrada configurada que puede representar una base distinta o un prefijo. El adaptador CRUD captura `DB::getCurrentConnectionId()` después de resolver la conexión; matching de subscriptions y lectura de la fila previa deben usar ese mismo alias y restaurar la conexión previa al terminar. No se agrega una columna ni se interpreta `belongs_to` como tenant.
- El owner es una frontera independiente. Para entidades cuyo modelo tiene el campo devuelto por `belongsTo()`, una entrega singular solo puede coincidir con subscriptions cuyo campo `belongs_to` identifica al mismo owner de la fila persistida. El owner se deriva de esa fila, no de `actorId`; un owner null explícito solo coincide con subscriptions de owner null. Si no se puede resolver el owner, no se envía a subscriptions con owner.
- Para un evento `list` owner-aware, cada subscription recibe únicamente las filas cuyo campo owner coincide con su `belongs_to`. Si falta el campo necesario para separar filas, el matcher falla cerrado para ese evento owner-aware. Una entidad sin campo owner debe marcar el scope como global de forma explícita y solo puede coincidir con subscriptions de la misma conexión.
- `WebhookEvent::scopeContext` es metadato interno confiable, no entrada de usuario ni prueba de autorización. `ApiController` deriva `connection_id` y el modo de owner después de aplicar sus ACL. Los publishers internos deben declarar el owner/scope global de forma explícita; no se infiere ownership desde `actorId`. Una conexión no default debe identificarse expresamente.
- En `update`, resolver owner es una lectura de scope separada: no incorpora columnas previas a los datos usados por las condiciones y conserva la semántica legacy ya cubierta por pruebas. Para update se usa el owner del estado persistido después de la operación; para delete se lee la fila borrada incluida en el scope del matcher.
- Las lecturas y escrituras CRUD de subscriptions continúan aplicando el scope de owner existente en `ApiController`; el matcher debe añadir el mismo límite de owner a la selección de deliveries. No se cambia el comportamiento ACL general ni el valor actual `restrict_by_tenant`.
