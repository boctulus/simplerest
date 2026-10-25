---
title: PACK Hardening and Distribution Contract
current_step: 3
next_step: null
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: null
tags: [pack, composer, distribution, safety]
---
## Pasos planificados

1. **Inventory and safety baseline** — Inspect every PACK entrypoint, alias, consumer, test, release script, source status, and the existing destination without modifying it.
2. **Authoritative CLI routing** — Make Composer core build and both dry-run forms use the immutable core builder; isolate the legacy starter workflow and repair help/error handling.
3. **Source preflight** — Add exact-SHA reporting, relevant dirty-source rejection for default builds, and explicit historical `--source-commit` behavior.
4. **Destination transaction safety** — Add ownership/layout recognition, Git/untracked/ignored-file checks, overlap/link protection, staging, rollback-safe replacement, and refusal of legacy destructive migration.
5. **Content and provenance contract** — Enforce the approved microcore boundary, hashes/source metadata, and compatibility with release verification without publishing anything.
6. **Focused regression suite** — Cover the requested safety, dry-run, CLI, output-boundary, and installed-artifact scenarios using disposable directories.
7. **Canonical documentation alignment** — Create the canonical PACK contract, refresh the documentation inventory, and align README, CLAUDE.md, CLI help, and release documentation.
8. **Strict verification and scoped commits** — Run focused/full applicable tests, Composer artifact verification, documentation governance, diff/encoding checks, then commit only owned files on `master`.
