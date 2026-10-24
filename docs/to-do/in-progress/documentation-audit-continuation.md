---
title: Continue documentation audit from current implementation
current_step: 2
next_step: 3
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: high
tags: [documentation, audit, docs]
---
## Organización editorial acordada (2026-10-24)

La documentación pública se organiza por dominios de uso del framework, con una página canónica dueña para cada contrato. No se publica el archivo histórico completo como si fuera una referencia vigente ni se crea una página por cada clase trivial.

1. **Inicio y arquitectura** — instalación reproducida, bootstrap y selección global de entrada.
2. **Core y HTTP** — ciclo HTTP, Request/Response, routing, middleware, ApiClient, validación, errores y helpers.
3. **Datos** — conexiones, Query Builder, Model/ModelRecord, relaciones, schemas, migraciones y subrecursos.
4. **API y seguridad** — endpoints automáticos, CRUD, Files, autenticación, ACL y webhooks.
5. **Servicios** — cache, EventBus, vistas, i18n y formularios.
6. **Operación y referencia** — CLI, configuración, paquetes, integraciones, despliegue y catálogo de APIs públicas.

El mapa español enlaza únicamente fuentes revisadas. VitePress replica esas fuentes con `source_revision` verificable; afirmaciones sin ejecución reproducible permanecen marcadas como parciales o pendientes.

## Pasos planificados

1. **Correct prior audit findings** — replace compound findings with claim-level statuses and evidence; correct the PHPUnit statement and routing sequence in canonical pages; record API permission anomalies without changing implementation.
2. **Installation and bootstrap** — inventory legacy claims, trace app and Composer-consumer boot paths, attempt clean reproductions without secrets, update the register, then write only evidence-backed canonical guidance and check links.
3. **Routing and request lifecycle** — audit entry points, route precedence, short-circuit behavior, request parsing, handlers, and covered tests; update ledger, canonical reference, links, and commit.
4. **Database connections, Query Builder, and Model** — audit connection configuration, query behavior, Model/ModelRecord composition, and relevant tests; write distinct canonical references for Query Builder and Model, update ledger, links, and commit.
5. **Schemas and automatic API** — audit schema/model/API generators, resolver and dispatch rules, generated templates, and tests; preserve end-to-end workflow as unresolved until run in a controlled setup; update canonical reference and commit.
6. **Authentication and ACL** — audit auth flows, permissions, configuration, and tests; investigate POST/PATCH callable anomalies without modifying implementation; document findings and commit.
7. **CLI and migrations** — audit command discovery, command behavior, migration configuration and tests; only publish runnable examples that were executed; update canonical pages and commit.
8. **ApiClient, integrations, deployment, and performance** — document ApiClient as its own core reference, then audit remaining topics in dependency order, separating implemented behavior from environment-dependent claims and measurements; update canonical pages, links, and commit each topic separately.
