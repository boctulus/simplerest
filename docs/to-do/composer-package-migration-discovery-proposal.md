---
title: "Proposal: Composer-aware package migration discovery and reporting"
status: proposal
current_step: "Awaiting owner architecture review; no implementation authorized"
next_step: "Review migration identity, Composer/local package discovery, CLI behavior, and GoProp regression before approving any code changes"
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: false
next_step_complexity: high
tags: [migrations, composer, packages, cli, architecture, proposal, needs-review]
---

# Proposal: Composer-aware package migration discovery and reporting

> **PROPOSAL ONLY — NOT APPROVED FOR IMPLEMENTATION.**
> This file records a design idea, not an assigned development task. Do not implement, execute migrations, edit migration registry rows, change deployment gates, create migrations, or deploy based on this document without explicit owner review and approval. Examples below describe possible future behavior, **not existing SimpleRest features**.

**Recorded:** 2026-10-23  
**Target:** SimpleRest framework; GoProp is the motivating consumer and required regression case.  
**Status:** Pending architecture and production-safety review.

## 1. Problem and motivation

SimpleRest supports migration commands for the application's own migration directory, plus explicit package/module-oriented operations. A project that uses Composer dependencies and local PSR-4 packages also needs to **see** migrations belonging to installed and enabled packages, even when the default migrate command intentionally executes only application/core migrations.

A project can otherwise report "zero pending migrations" while an enabled package has migrations that were not applied. Conversely, if a package's migration was executed explicitly from a different directory, a status command that scans only the application's migration directory may misclassify its recorded execution as "orphaned".

The objective is **automatic discovery and transparent reporting**, not automatic schema modification.

## 2. Concrete incident: GoProp, 2026-10-23

Production page: https://goprop.app/dashboard/migrations

GoProp's production maintenance screen displayed, for its default main connection:

- 222 applied migrations (those found in the scanned core directory).
- 0 pending.
- 2 "orphaned" registry rows reported as "files missing from disk".

The affected filenames were:

1. 2026_09_02_000001_organization_administration.php
2. 2026_09_07_210000000_drop_administrator_invitation_conflict_key.php

**These files were found in GoProp's repository**, not deleted:

~~~text
packages/boctulus/organization-administration/database/migrations/
  2026_09_02_000001_organization_administration.php
  2026_09_07_210000000_drop_administrator_invitation_conflict_key.php
~~~

GoProp's existing maintenance status implementation in app/Services/Maintenance/MigrationRunner.php scans the default database/migrations directory, then compares those filenames to the migration registry. The two package files are not in that directory. The project's .agents/skills/deployment/SKILL.md already documents that normal migration discovery skips package migrations and that the resulting status can report them as orphaned.

The package is wired locally via the root composer.json PSR-4 autoload mapping and a ServiceProvider configured in config/config.php; it is **not** necessarily discoverable merely by iterating the root composer.json require entries. The second migration deliberately drops the obsolete conflict_key column and unique index from organization_administrator_invitations, with a replacement organization index. It is an actual schema evolution, not a disposable record.

**Expected resolution:** Treat these as known package migrations and reconcile them with their existing registry records. Do **not** delete the two rows to silence the warning, nor rerun their up() methods merely because a new catalog cannot yet match their location. Confirm the exact active production release, registry contents, and expected schema before declaring the incident resolved.

**Regression acceptance case:** Given those two physical package files and their previously recorded executions, package-aware status must recognize them as applied (assuming unambiguous registry identity and verified connection), not as missing/orphaned; it must not attempt to reapply them. On the reported snapshot, the expected **combined** count would be 224 applied, 0 pending, and 0 unexplained orphans, contingent on live verification.

Relevant source references:

- https://github.com/boctulus/goprop/blob/main/app/Services/Maintenance/MigrationRunner.php
- https://github.com/boctulus/goprop/blob/main/.agents/skills/deployment/SKILL.md
- https://github.com/boctulus/goprop/tree/main/packages/boctulus/organization-administration/database/migrations

## 3. Model installed, enabled and migrated independently

These states must not be conflated:

- **Installed:** Composer reports the package as installed, or the project explicitly declares an in-repository local package. Mere presence of an arbitrary folder is insufficient.
- **Enabled:** The application has explicitly activated the package (for example via a configured/registered ServiceProvider or another approved package registry). An installed package may be disabled.
- **Migrated:** All declared migrations of the package have matching applied records for the selected database connection. This is a registry state and **does not alone prove** that the expected schema exists.

Some installed-but-disabled packages may be displayed in a separate informational section, without counting their migrations as mandatory operational pending work. Historic records of packages that have since been removed should be preserved and classified as historical/unknown-origin until reconciliation, not automatically deleted.

## 4. Proposed discovery architecture (subject to review)

Create or extend a shared read-only **MigrationCatalog** which discovers migration **sources**, with one descriptor per source:

- Application/core directory.
- Composer-installed packages (use installed Composer metadata such as Composer\\InstalledVersions where appropriate).
- Explicitly declared local packages from the root application's configuration/PSR-4 registration.
- Explicit ServiceProvider migration directory declarations, analogous to Laravel's loadMigrationsFrom() model.

Provider-style declaration is a possible interface, not an implementation commitment:

~~~php
public function boot(): void
{
    $this->loadMigrationsFrom(
        __DIR__ . '/../database/migrations'
    );
}
~~~

Alternatively, support declarative package metadata; exact key names remain an open design decision:

~~~json
{
  "extra": {
    "simplerest": {
      "migrations": ["database/migrations"]
    }
  }
}
~~~

The catalog must have a controlled allowlist of recognized roots. **Do not recursively execute or indiscriminately scan all PHP files under vendor/ or packages/**. Prefer metadata enumeration; listing status must not require executing package migration scripts or booting arbitrary untrusted providers for discovery. Normalize and validate paths, prevent traversal, and support vendor-distributed packages and local development layouts.

The discovery layer should return structured metadata such as package name, source type, enabled state, normalized relative migration directory, migration filename and resolved path. The migration runner then reconciles that catalog against the registry, with the existing connection discriminator rules intact.

## 5. Identity, provenance and backward compatibility

The current registry uses at least filename and path information, plus the selected database discriminator in consuming applications. A filename alone is not necessarily unique across multiple packages. Proposal:

- Define a stable logical migration identity (for example, package identifier + relative migration file identity), independent of absolute deployment/release paths.
- Detect duplicate filenames/identities across sources; report a hard ambiguity rather than choosing one silently.
- Preserve backward compatibility for existing filename-based records. Where migration records are unambiguous, reconcile to their package source without executing them again.
- Consider a future origin/source field or separate catalog mapping, **only after** inspecting historical schema and rollback/deploy compatibility. No unreviewed migrations to the migration registry itself.
- Distinguish "known package migration applied", "active package migration pending", "package removed / historical application", "missing file with unknown origin", and "ambiguous/duplicate identity".
- Keep absolute paths out of durable identity; production releases use changing physical directories.
- Treat legacy path values carefully: they may be null, relative or absolute. Do not infer execution correctness from a path string alone.

A status check can report discrepancies, but any change to historical records requires a separate audited and approved reconciliation process. **No automatic deletion, rollback, retry or registry rewrite.**

## 6. Proposed CLI behavior — safe by default

| Command | Proposed behavior |
| --- | --- |
| php com migrations list | Preserve current listing, append a clearly labeled read-only section for enabled package migrations, including source/package and applied/pending state per connection. Adding --to for list requires CLI contract review. |
| php com migrations status | Combine core and recognized enabled package visibility; show separate subtotals, unresolved orphans, disabled/removed packages and ambiguities. |
| php com migrations migrate | Continue migrating only application/core migrations by default. Afterwards **report** enabled package migrations still pending. Do not run them implicitly. |
| php com migrations migrate-package NAME | Preserve explicit execution of a named package; adapt path resolution to Composer packages and local registered packages, only after testing. |
| php com migrations migrate --include-packages | Possible future opt-in execution, **not approved**. Requires dependency ordering, locking and safety review. |
| php com migrations migrate-packages | Alternative possible opt-in command; decide on **one** public execution surface before implementation. |
| php com migrations status --strict | Possible future nonzero exit when required active-package migrations remain; strict policy and deployment-gate compatibility require approval. |

The final section of list/status/migrate should include package name, source and migration filenames. For example:

~~~text
PACKAGE MIGRATIONS (enabled; connection: main)
boctulus/organization-administration
  [APPLIED] 2026_09_02_000001_organization_administration.php
  [APPLIED] 2026_09_07_210000000_drop_administrator_invitation_conflict_key.php

Pending package migrations: 0
Unexplained orphan registry entries: 0
~~~

If pending entries exist, show exact filenames and the explicit relevant command, but do not run them as a side effect of listing or core migration execution. A command reporting "success" must not conceal a package-pending finding. Decide whether that is informational by default and how a separate strict deploy gate should fail.

## 7. GoProp UI and deployment implications (not in scope for this proposal's implementation)

The GoProp migration dashboard should eventually consume the **same** catalog and connection-specific status logic as the CLI; it must not independently guess package state. Separate applied/core, applied/package, pending/core, pending/package, and genuinely unexplained orphans. Display the package/source for each file.

Do not add a bulk-delete checkbox as the fix for the GoProp case: those registry rows represent real package migrations. A future history-cleanup feature, if approved, must require superadmin authorization, per-connection controls, audit logs, revalidation and explicit confirmation that it changes only the registry (never runs down()).

Deployment gates must not silently pass missing **required** package migrations. However, altering release gates is explicitly **out of scope until separately reviewed**. Assess staging, production, existing tenants, and disabled/optional packages before deciding strict behavior.

## 8. Verification plan, only after owner approval

1. Inventory the current SimpleRest CLI, registry schema, command parsers, Composer/package loading, ServiceProvider lifecycle, and existing migration tests on the branch selected for implementation.
2. Validate core-only discovery and execution remain backward compatible.
3. Cover Composer-installed packages, local PSR-4 packages, enabled/disabled states, packages with no migrations, and absent or misconfigured declared directories.
4. Cover two packages with identical filenames, ambiguous historic records, renamed paths, removed packages, and unexpected/symlink-traversed paths.
5. Cover default and non-default database connections and the correct registry discriminator (including null for default, where applicable).
6. Add a **read-only GoProp regression fixture** for both organization-administration migration filenames and their physical package location, without touching production.
7. Verify list/status never execute migration PHP; core migrate never runs package migrations without explicit opt-in.
8. Review idempotency, transaction semantics, concurrent migration locking, dry runs, rollback scope and deployment gates before any new execution command is considered.
9. Run automated isolated tests in disposable databases; manually approve production verification. No live migration/cleanup as part of evaluating this proposal.

## 9. Open decisions requiring owner review

- Should local package activation be derived from configured ServiceProviders, an explicit manifest/registry, or both?
- Which SimpleRest package metadata convention should be supported? How does this align with the ongoing Composer modularization?
- Is package registration via loadMigrationsFrom() appropriate, or should read-only manifests be preferred to avoid boot-time side effects?
- How should existing filename-only migration records be mapped to a stable owner-qualified identity safely?
- Should core migrate only warn, or also be able to return a strict exit code under an explicit flag?
- For deployment, which package migrations are required versus optional, and how are dependencies ordered?
- Should the GoProp dashboard display inactive/uninstalled historical packages without warning, and how can owners verify historical state?

## 10. Authorization boundary

**Nothing in this document authorizes implementation or execution.** This is a proposed design for review only. Do not update code, CLI behavior, Composer manifests, database schema, registry rows, GoProp production, or deployment scripts on its basis. After explicit approval, write an implementation plan and validate it against current framework/GoProp integration and Composer distribution work before coding.
