---
title: Continue documentation audit from current implementation
current_step: 4
next_step: 5
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
3. **HTTP routing and request lifecycle** — audit entry points, WebRouter route forms/precedence, short-circuit behavior, Request/Response APIs, handlers, and covered tests; keep CliRouter in its own guide; update ledger, canonical references, links, and commit.
4. **Database connections, Query Builder, and Model** — audit connection configuration, query behavior, Model/ModelRecord composition, and relevant tests; write distinct canonical references for Query Builder and Model, update ledger, links, and commit.
5. **Schemas and automatic API** — audit schema/model/API generators, resolver and dispatch rules, generated templates, and tests; preserve end-to-end workflow as unresolved until run in a controlled setup; update canonical reference and commit.
6. **Authentication and ACL** — audit auth flows, permissions, configuration, and tests; investigate POST/PATCH callable anomalies without modifying implementation; document findings and commit.
7. **CLI commands and migrations** — keep `CliRouter` separate from the `php com` command registry; audit command discovery, command behavior, migration configuration and tests; only publish runnable examples that were executed; update canonical pages and commit.
8. **ApiClient, integrations, deployment, and performance** — ApiClient now has its own core reference; audit integrations, deployment, and performance in dependency order, separating implemented behavior from environment-dependent claims and measurements; update canonical pages, links, and commit each topic separately.

## Estado de cobertura

- **Inicio/bootstrap:** auditoría de instalación y arranque con un flujo HTTP mínimo reproducido; el consumo como dependencia Composer y el CRUD con base de datos siguen sin validar.
- **Core/HTTP:** el ciclo HTTP está separado del recorrido CLI; WebRouter, Request/Response y ApiClient tienen referencias propias con límites de fuente explícitos. Middleware y otras piezas del core siguen en cola.
- **Datos:** conexiones, Query Builder, Model/ModelRecord y descriptores de esquema tienen referencias; no se ejecutó el conjunto de consultas sobre una base real. Relaciones, migraciones, subrecursos, transacciones y drivers siguen pendientes.
- **API/seguridad:** resolución de endpoints, autenticación, ACL y webhooks están trazados con sus límites; faltan recorridos completos de CRUD/autorización con una base configurada.
- **CLI/operación/servicios:** CliRouter tiene referencia propia; inventario de `php com`, migraciones, cache, EventBus, vistas, i18n, formularios, integraciones y despliegue todavía requiere revisión y referencias de uso.

La cobertura fuente no se marca como reproducción funcional. Cada área pendiente conserva esa distinción hasta tener evidencia propia.
