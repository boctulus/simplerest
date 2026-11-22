---
title: CLI, skeleton y publicación de docs/skills para proyectos consumidores (estilo Laravel)
current_step: "Paso 1 completado; siguiente: vendor/bin/simplerest init"
next_step: "Paso 2: vendor/bin/simplerest init (bootstrap mínimo en proyectos existentes)"
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: false
next_step_complexity: high
tags: [composer, cli, distribution, skeleton, docs, skills]
---
## Contexto

`boctulus/simplerest` 3.0.5 (publicado el 2026-11-22) es solo la librería del core (`src/framework`), según `docs/architecture/pack-contract.md`. Un proyecto que lo instala con `composer require` (por ejemplo `D:\laragon\www\quality`) no recibe `com`, `app.php`, `config/` ni `app/Commands`, así que `php com` falla. Composer tampoco ejecuta los `scripts` de una dependencia (solo los del paquete raíz), así que el core no puede copiar archivos al instalarse.

Decisión del maintainer (2026-11-22): seguir el modelo de Laravel.

| Laravel | SimpleRest |
| --- | --- |
| `laravel/framework` (librería) | `boctulus/simplerest` (core, sin cambios) |
| comandos de Artisan dentro del framework | nuevo `boctulus/simplerest-cli` con los comandos de uso general |
| `laravel/laravel` (skeleton, `create-project`) | nuevo `boctulus/simplerest-skeleton` |
| `php artisan vendor:publish` / `boost:install` | `php com publish docs` / `php com publish skills` |

## Diseño

- **`boctulus/simplerest-cli`** (`packages/boctulus/simplerest-cli`, igual que `simplerest-scaffolding`):
  - `src/Commands/`: los grupos de uso general movidos desde `app/Commands`, que el `CommandRegistry` existente descubre mediante `config/commands.php` → `packages`.
  - `bin/simplerest`, que Composer expone como `vendor/bin/simplerest`. `vendor/bin/simplerest init` instala el bootstrap mínimo en un proyecto existente: `com`, `app.php`, `config/*`, `scripts/init/boot`, `.env.example` y los directorios de `app/`. Nunca sobrescribe un archivo existente: lo reporta y lo saltea.
  - grupo `publish`: `publish docs` copia a `docs/framework/` y `publish skills` copia a `.agents/skills/`, en ambos casos solo lo que está en la allowlist. Se registra un manifiesto con hashes (`.simplerest/published.json`). Un archivo editado por el usuario no se pisa sin `--force`.
  - `resources/docs/` y `resources/skills/`: copias commiteadas de la allowlist. El comando de desarrollo `publish sync-resources` las mantiene sincronizadas y un test falla si difieren del monorepo.
- **`boctulus/simplerest-skeleton`**: `composer.json` de tipo `project` que requiere `boctulus/simplerest ^3.0` y `boctulus/simplerest-cli`. Su `post-create-project-cmd` corre `vendor/bin/simplerest init` y `publish docs/skills`, para que los stubs existan en un solo lugar (el CLI).
- **Core**: no cambia. `unit-tests/microcore` y `pack-contract.md` siguen vigentes.
- **Monorepo**: `app/Commands` conserva solo las herramientas del monorepo. Durante el desarrollo, el monorepo habilita `boctulus/simplerest-cli` en `config/commands.php`, así `php com` sigue mostrando todo.

## Allowlist propuesta (pendiente de aprobación)

**Grupos de comandos que van al CLI:** `migrations`, `sql`, `mysql`, `file`, `module`, `router`, `system`, `users`, `roles`, `acl`.
**Se quedan en el monorepo:** `pack`, `todo`, `skill`, `doc`.

**Docs → `docs/framework/`:** `docs/getting-started`, `docs/core`, `docs/cli`, `docs/api`, `docs/reference`, `docs/security` (sin `docs/audit`, `_archive`, `_internal`, `to-do`, `issues` ni la documentación de paquetes no instalados).

**Skills → `.agents/skills/`:**
- Framework (incluir): acl-config, acl-operations, api-client, auth-consumption, automatic-endpoints, caching, command-design-conventions, create-admin-page, create-api-endpoint-guide, deployment, eventbus-hooks, form-field-validation, helpers, i18n, middleware, migration-lifecycle, multi-tenant-config, package-creator, query-builder, related-table-filter, request-response, routing, schemas, security-hardening, standalone-script, subresources, validation-rules, view-engine.
- Excluir (otro stack o herramienta personal): svelte-custom-component-governance, external-svg-asset-enforcement, production-deployment-protocol (Node/PM2), push-notifications-architecture, gpt-blocking-decision-consult, git-worktree-policies, web-design-guidelines, ui-replication-from-design, release-deploy-protocol, todo-kanban, task-journaling, skill-maker, skill-orchestrator, skill-index-maintainer, skill-reviewer-protocol, skill-conflict-resolution-protocol.
- A decidir (genéricos de proceso/calidad): anti-hallucination-project-guard, code-defensive-refactoring, code-naming-conventions-contract, code-quality-protocol, cross-layer-naming-consistency-contract, fresh-research-protocol, git-safety-protocol, git-snapshot-verification, incident-docs-protocol, internal-link-normalizer, post-task-verification(-strict), prevent-interface-inflation, prompt-execution-control-protocol, prompt-output-constraints-contract, task-decomposition-protocol, test-data-governance-and-safety, adaptive-datagrid-contract, alert-usage-contract, ui-consistency-and-pattern-reuse.

## Pasos planificados

1. ✅ **Paquete `simplerest-cli` con los comandos** (2026-11-22: 10 grupos movidos con `git mv`; `php com` y la ayuda de cada grupo son idénticos a la línea base; `unit-tests/command` 11/11; tests del paquete 2/2. Se corrigió además `migrations make`, que hacía `require_once` de `../make/MigrationCommand.php`, un archivo inexistente; ahora usa la clase de scaffolding o falla con un mensaje claro): mover los grupos aprobados y habilitar el paquete en el monorepo. Prueba: `php com` lista los mismos grupos antes y después, y corren los tests existentes de los comandos.
2. **`vendor/bin/simplerest init`**: stubs de bootstrap a partir de los archivos actuales del monorepo (`com`, `app.php`, `config/`, `scripts/init/boot`), sin datos sensibles ni configuración local. Prueba: en un consumidor limpio con `composer require`, `init` y luego `php com` y `php com migrations help` funcionan, y correr `init` dos veces no pisa nada.
3. **Grupo `publish` (docs/skills) y `sync-resources`**: allowlist, manifiesto con hashes, `--force` y test de sincronización.
4. **`simplerest-skeleton`**: `composer create-project` desde un repositorio VCS local; luego `php com`, `docs/framework/` y `.agents/skills/` presentes y `GET /` responde.
5. **Docs**: `docs/packages/`, `docs/cli/README.md`, `docs/getting-started` y la sección de releases de `composer-release-process.md`.
6. **Releases** (cada uno con autorización explícita): repositorios GitHub y Packagist para `simplerest-cli` y `simplerest-skeleton`, y migración de `quality` con `vendor/bin/simplerest init`.

## Incertidumbre registrada

Es un cambio de arquitectura de distribución. Este entorno no tiene acceso a la consulta GPT de `gpt-architecture-consult`; la decisión se tomó con criterio propio y la elección explícita del maintainer.
