---
title: Continue documentation audit from current implementation
current_step: 9
next_step: 10
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
5. **Schemas and automatic API** — audit schema/model/API generators, DDL builder methods, resolver and dispatch rules, GET parameter conversion, generated templates, and tests; keep the generator-to-CRUD workflow unresolved until run in a controlled setup; publish separate schema-descriptor, DDL, and API references with source/runtime boundaries.
6. **Authentication and ACL** — audit auth flows, permissions, configuration, and tests; investigate POST/PATCH callable anomalies without modifying implementation; document findings and commit.
7. **CLI commands and migrations** — keep `CliRouter` separate from the `php com` command registry; audit command discovery, command behavior, migration configuration and tests; only publish runnable examples that were executed; update canonical pages and commit.
8. **Services** — audit cache, EventBus, views, i18n, and forms against their implementation and tests; publish separate usage references with environment/runtime limits and update the canonical map.
9. **ApiClient, integrations, deployment, and performance** — ApiClient now has its own core reference; audit its remaining contract, integrations, deployment, and performance in dependency order, separating implemented behavior from environment-dependent claims and measurements; update canonical pages, links, and commit each topic separately.
10. **Database remainder** — complete the still-pending relation and pivot APIs, transaction behavior, raw-query effects, pagination/aggregates, automatic joins, tenant isolation, and per-driver compatibility. Map each claim to source and focused tests; keep database execution unverified unless it is run against an approved disposable database.
11. **Core runtime and HTTP remainder** — document middleware contracts, closure-route middleware, handler extension points, CORS, configuration/environment loading, service providers/container, sessions/cookies, logging, error handling, and relevant exception families. Split pages by responsibility rather than placing unrelated components in a request-lifecycle narrative.
12. **Files, uploads, and output utilities** — audit upload/download helpers, filesystem paths, static assets, media/image, ZIP, backup, XML/HTML, pagination, and response formatters. Describe path and trust boundaries; do not claim a safe upload or deployment path without a reproduced configuration.
13. **API and security extensions** — cover resource controllers and serialization, collections/trashcan, automatic CRUD edges, JWT, basic/fine-grained ACL packages, CORS, and security utilities. Keep optional package behavior and core behavior in separate references.
14. **Packages and integrations catalog** — classify all Composer packages by capability and support status. Write package guides in groups where the APIs share a contract, and individual guides for integrations with distinct configuration or network behavior. Identify unverified installability, provider credentials, external side effects, and legacy packages.
15. **CLI and developer workflows remainder** — expand the existing `php com` catalog where the command registry exposes material argument/side-effect contracts, document scaffolding and generators, and distinguish application commands from `CliRouter` routes. Do not run commands that write project or database state without an approved disposable target.
16. **Public API reference and closure audit** — replace the reference placeholder with categorized configuration keys, environment variables, global helpers, command entry points, interfaces/traits, and public class families. Map every PHP file under `src/framework` and each package under `packages/boctulus` to an owning guide or an explicit internal/legacy classification; refresh inventory and public VitePress navigation and close the audit only after link/build checks.

## Estado de cobertura

- **Inicio/bootstrap:** auditoría de instalación y arranque con un flujo HTTP mínimo reproducido; el consumo como dependencia Composer y el CRUD con base de datos siguen sin validar.
- **Core/HTTP:** el ciclo HTTP está separado del recorrido CLI; WebRouter, Request/Response y ApiClient tienen referencias propias con límites de fuente explícitos. Middleware y otras piezas del core siguen en cola.
- **Datos:** conexiones, Query Builder, Model/ModelRecord, descriptores, constructor DDL y migraciones tienen referencias separadas; no se ejecutaron consultas, DDL ni migraciones sobre una base real. Relaciones, subrecursos, transacciones y drivers siguen pendientes.
- **API/seguridad:** resolución de endpoints y conversión de parámetros GET están trazadas en fuente, junto con autenticación, ACL y webhooks; faltan peticiones reales y recorridos CRUD/autorización con una base configurada.
- **CLI/migraciones:** CliRouter, `CommandRegistry`/`php com` y el ciclo de migraciones tienen referencias separadas. Se consultó la ayuda de CLI; no se ejecutó ningún comando operativo ni migración.
- **Servicios:** referencias canónicas y públicas separadas para cache, EventBus, vistas, i18n, validación del servidor y builders HTML/formularios; se documentaron diferencias entre drivers, dependencias Composer y límites de escape. Fuente auditada; no se ejecutaron operaciones de cache, render, formulario ni traducción.
- **Integraciones/operación:** ApiClient ya tiene una referencia propia; faltan completar la auditoría de integraciones, despliegue y rendimiento.

La cobertura fuente no se marca como reproducción funcional. Cada área pendiente conserva esa distinción hasta tener evidencia propia.

## Criterio de cierre global

El checkout actual contiene 212 archivos PHP bajo `src/framework` y 24 paquetes Composer con 133 archivos PHP bajo `packages/boctulus`. Son conteos de inventario, no una medida de cobertura. La tarea queda completa cuando cada familia pública de esos árboles tiene una guía dueña, cada componente interno o legacy queda clasificado explícitamente, las guías están enlazadas desde sus índices canónicos y públicos, y el inventario de Markdown no tiene enlaces locales rotos. Una referencia basada en fuente no se presenta como prueba de instalación, red, base de datos o despliegue.
