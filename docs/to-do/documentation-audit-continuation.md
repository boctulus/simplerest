---
title: Continue documentation audit from current implementation
current_step: 1
next_step: null
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: null
tags: [documentation, audit, docs]
---
## Pasos planificados

1. **Correct prior audit findings** — replace compound findings with claim-level statuses and evidence; correct the PHPUnit statement and routing sequence in canonical pages; record API permission anomalies without changing implementation.
2. **Installation and bootstrap** — inventory legacy claims, trace app and Composer-consumer boot paths, attempt clean reproductions without secrets, update the register, then write only evidence-backed canonical guidance and check links.
3. **Routing and request lifecycle** — audit entry points, route precedence, short-circuit behavior, request parsing, handlers, and covered tests; update ledger, canonical reference, links, and commit.
4. **Database connections and Query Builder** — audit connection configuration and query behavior with relevant tests; update ledger, canonical reference, links, and commit.
5. **Schemas and automatic API** — audit schema/model/API generators, resolver and dispatch rules, generated templates, and tests; preserve end-to-end workflow as unresolved until run in a controlled setup; update canonical reference and commit.
6. **Authentication and ACL** — audit auth flows, permissions, configuration, and tests; investigate POST/PATCH callable anomalies without modifying implementation; document findings and commit.
7. **CLI and migrations** — audit command discovery, command behavior, migration configuration and tests; only publish runnable examples that were executed; update canonical pages and commit.
8. **Integrations, deployment, and performance** — audit in dependency order, separating implemented behavior from environment-dependent claims and measurements; update canonical pages, links, and commit each topic separately.
