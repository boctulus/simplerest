---
title: Continue documentation audit from current implementation
current_step: 14
next_step: 15
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
12. **Files, uploads, and output utilities — complete** — audited upload/download helpers, filesystem paths, static assets, media/image, ZIP, backup, XML/HTML, pagination, and response formatters. Added separate references with trust boundaries; runtime operations remain unverified.
13. **API and security extensions — complete** — added separate source references for resource controllers, Collections, TrashCan, file endpoints, JWT, ACL implementations, and the authorization engine. Linked the API CORS integration to the core CORS guide; kept the HTTP lifecycle separate from CLI routing. Recorded source-level authorization and route-link discrepancies. No API, database, upload, download, or authentication operation was run.
14. **Packages and integrations catalog — in progress** — classify all Composer packages by capability and support status. Write package guides in groups where the APIs share a contract, and individual guides for integrations with distinct configuration or network behavior. Identify unverified installability, provider credentials, external side effects, and legacy packages.
15. **CLI and developer workflows remainder** — expand the existing `php com` catalog where the command registry exposes material argument/side-effect contracts, document scaffolding and generators, and distinguish application commands from `CliRouter` routes. Do not run commands that write project or database state without an approved disposable target.
16. **Public API reference and closure audit** — replace the reference placeholder with categorized configuration keys, environment variables, global helpers, command entry points, interfaces/traits, and public class families. Map every PHP file under `src/framework` and each package under `packages/boctulus` to an owning guide or an explicit internal/legacy classification; refresh inventory and public VitePress navigation and close the audit only after link/build checks.

## Estado de cobertura

- **Inicio/bootstrap:** auditoría de instalación y arranque con un flujo HTTP mínimo reproducido; el consumo como dependencia Composer y el CRUD con base de datos siguen sin validar.
- **Core/HTTP:** el ciclo HTTP está separado del orden global de resolvers, que vive en Arquitectura. WebRouter, Request/Response, ApiClient, middleware, handlers, configuración/entorno, providers, Container, CORS, sesiones/cookies, errores y logging tienen referencias por responsabilidad. Son trazas de fuente; no se ejecutaron sus recorridos de runtime.
- **Datos:** conexiones, Query Builder, Model/ModelRecord, relaciones/subrecursos, SQL raw, transacciones, compatibilidad por driver, descriptores, constructor DDL y migraciones tienen referencias separadas. No se ejecutaron consultas, escrituras, transacciones, DDL ni migraciones; las pruebas relacionadas sólo se inspeccionaron.
- **API/seguridad:** el paso 13 quedó documentado con guías separadas para controladores, Collections, TrashCan, endpoints de archivos, JWT, implementaciones ACL y motor de autorización. Se registraron los límites de propiedad en Files/Collections, la discrepancia `/get/{id}` frente a `/download/{id}` y la ruta real del controlador JWT. Faltan peticiones API, autenticación y CRUD con base configurada.
- **CLI/migraciones:** CliRouter, `CommandRegistry`/`php com` y el ciclo de migraciones tienen referencias separadas. Se consultó la ayuda de CLI; no se ejecutó ningún comando operativo ni migración.
- **Servicios:** referencias canónicas y públicas separadas para cache, EventBus, vistas, i18n, validación del servidor y builders HTML/formularios; se documentaron diferencias entre drivers, dependencias Composer y límites de escape. Fuente auditada; no se ejecutaron operaciones de cache, render, formulario ni traducción.
- **Integraciones/operación:** ApiClient, API Clients, Shopify, LLM Providers, despliegue y rendimiento tienen referencias canónicas y derivadas. El build de VitePress pasa; no se ejecutaron llamadas externas, benchmarks ni despliegues productivos.
- **Datos restantes:** el paso 10 quedó documentado a nivel de fuente. Relaciones/pivots, transacciones, SQL raw, paginación/agregados, joins automáticos, aislamiento tenant y drivers conservan límites de ejecución no verificados; el paso 11 sigue con el runtime y HTTP.
- **Runtime core:** el paso 11 quedó documentado y clasificado en el ledger. Permanecen sin reproducir middleware, CORS, sesiones/cookies, contenedor, proveedores, logging y manejo de errores; la revisión del renderer local confirma que la fuente pública actual del ciclo HTTP no contiene `CliRouter`.
- **Archivos/utilidades:** el paso 12 quedó publicado con referencias separadas para filesystem, uploads, ZIP/backups, conversión HTML/XML/imágenes y paginación. La fuente y fixtures se inspeccionaron; no se ejecutaron uploads, descargas, extracción, backups, parsing adversarial, respuestas de imagen, ni paginación en DB o memoria.

La cobertura fuente no se marca como reproducción funcional. Cada área pendiente conserva esa distinción hasta tener evidencia propia.

## Criterio de cierre global

El checkout actual contiene 212 archivos PHP bajo `src/framework` y 24 paquetes Composer con 133 archivos PHP bajo `packages/boctulus`. Son conteos de inventario, no una medida de cobertura. La tarea queda completa cuando cada familia pública de esos árboles tiene una guía dueña, cada componente interno o legacy queda clasificado explícitamente, las guías están enlazadas desde sus índices canónicos y públicos, y el inventario de Markdown no tiene enlaces locales rotos. Una referencia basada en fuente no se presenta como prueba de instalación, red, base de datos o despliegue.
