# Documentation audit and evidence rules

## Purpose

The previous documentation grew from a monolithic text file into multiple topic pages. Some pages contain claims that cannot be accepted without source evidence, and some describe proposed or external-framework architecture as though it were part of SimpleRest. This audit rebuilds the maintained documentation from the current implementation.

## Source of truth

Use current source code as the primary authority. Trace a behavior through its implementation and configuration. Use tests to establish covered behavior, and runnable examples to establish user-facing instructions. Existing Markdown and the archived `DOC-Simplerest.txt` are discovery material only. They do not validate themselves.

Record the evidence at the level needed for each claim:

| Evidence | What it can establish |
| --- | --- |
| Implementation path and symbol | The behavior or interface exists in this checkout |
| Configuration path and key | The behavior is enabled or altered under stated conditions |
| Test path and case | The tested scenario and its asserted result |
| Example or command run | The exact instruction works in the stated environment |

Do not infer untested behavior from a class name, directory structure, another framework, or an old guide. Mark unclear or environment-dependent statements as unresolved until evidence is available.

## Claim states

- **Verified** — implementation evidence is identified; required configuration and limits are stated. Tutorials also require a successful run of the documented steps.
- **Partial** — part of the claim is confirmed, but scope, conditions, or behavior remain unverified.
- **Unverified** — no sufficient source trace has been completed.
- **Rejected** — the claim conflicts with current implementation or describes a feature that is not present.
- **Historical** — retained to explain prior design or plans; not a statement about current behavior.

## Claim-level finding: PHPUnit discovery

| Claim | Evidence in this checkout | State | Documentation action |
| --- | --- | --- | --- |
| The Composer `test` script invokes PHPUnit | `composer.json` defines `scripts.test` as `phpunit --colors=always` | Verified (manifest only) | Describe the script exactly; do not infer which tests it discovers |
| `composer test` runs the `unit-tests/` directory | `phpunit.xml` names `tests/` and `packages/boctulus/friendlypos-web/tests`; the root `tests/` directory is absent, while `unit-tests/` exists but is not named by a configured suite | Rejected | Correct the root README and leave suite execution/discovery unresolved until run |
| The configured PHPUnit command is runnable and its result represents the repository test suite | The configuration has mismatched paths; no PHPUnit command was run in this audit | Unverified | Do not publish a pass/fail or coverage claim |

## Claim-level finding: routing entry-point short-circuit

| Claim | Evidence in this checkout | State | Documentation action |
| --- | --- | --- | --- |
| `index.php` checks enabled resolvers in web-router, CLI-router, front-controller order | `index.php` calls `WebRouter::resolve()`, `CliRouter::resolve()`, then `FrontController::resolve()` under the corresponding `config/config.php` switches; all three are true in the checked-in config | Verified (call order) | Do not imply every call runs for every request |
| A matched `WebRouter` route permits the later entry-point stages to run | `WebRouter::resolve()` dispatches a matching closure/controller and exits; non-CLI, missing-method-bucket, and no-match paths return instead | Rejected | State the route-match short-circuit and the no-match continuation separately |
| An unmatched HTTP request reaches `FrontController` when it is enabled | After a no-match return, `CliRouter::resolve()` returns immediately outside CLI; `index.php` then calls the enabled front controller | Verified for the checked-in switches and this no-match path | Keep other routing and deployment conditions explicit |
| Existing router tests establish entry-point order and process exit behavior | `WebRouterTest` covers registration/compilation/specificity; `WebRouterFunctionalTest` covers HTTP route outcomes, including a not-found case; neither asserts `index.php` resolver ordering or the process-level exit boundary | Unverified | Treat the entry-point behavior as source-traced, not test-covered |

## Editorial rules

1. Describe SimpleRest's own classes, configuration, and lifecycle. Do not present Laravel, Symfony, or another framework's architecture as SimpleRest architecture.
2. Keep plans and comparisons out of normative usage guides. Label historical proposals explicitly.
3. Give each concept one owning reference page. Tutorials link to that page.
4. Separate a Composer library dependency from installing and bootstrapping this repository as an application. Do not recommend either workflow until its actual setup is documented and verified.
5. A Quick Start is publishable as runnable only after its steps have been executed from a clean checkout or clean consumer project, as appropriate.
6. Avoid unmeasured performance numbers, unsupported compatibility matrices, and superlatives.

## Initial findings

| Claim or artifact | Evidence observed | State | Documentation action |
| --- | --- | --- | --- |
| “Laravel-like” syntax/architecture as product framing | README and philosophy pages use the comparison as a defining contract; no source trace establishes it as an architectural specification | Rejected as architecture; inspiration/comparison is historical context | Remove it from normative overview and explain SimpleRest using its own APIs |
| Fixed six-handler front-controller pipeline | `config/config.php` provides six configurable handler entries; `FrontController::resolve()` constructs and invokes them | Partial | Document the configured extension points; verify ordering and all execution paths before publishing a full lifecycle contract |
| Startup call order in `index.php` | `index.php` checks web router, CLI router, and front controller in that order; an earlier resolver can terminate the process before later checks | Verified for call order only | Document ordering separately from per-request execution |
| “Automatic CRUD for every table with a schema” | Existing guides make a broad claim; specific route, schema, database, and authorization conditions have not yet been tested end to end | Unverified | Trace resolver/controller/schema path and verify a clean example before documenting as a guarantee |
| Composer dependency installation is sufficient to create an application | `composer.json` identifies a library package and has core PSR-4 mappings; the repository bootstrap also loads application/configuration paths | Unverified | Keep library consumption and repository/application installation separate; validate both independently |
| Performance claims such as 3–10 ms bootstrap | No benchmark procedure or reproducible environment is cited in the public overview | Rejected as an unqualified fact | Remove until measured with a documented, reproducible benchmark |
| “Multiple database engines supported” list | README lists engines, but the current driver and feature matrix has not been traced in this pass | Unverified | Audit each driver against connection and query implementation, then state exact limits |

## Scope and next audit sequence

This is the initial baseline, not a completed audit of every topic. Next, audit in dependency order: clean installation and bootstrap; routing/request lifecycle; database connections and query behavior; schemas and automatic API resolution; authentication and ACL; CLI and migrations; then optional integrations, views, deployment, and performance. For each page, capture implementation, configuration, tests, and runnable evidence before publishing a verified status.

The archived original is [`../framework/_archive/DOC-Simplerest.txt`](../framework/_archive/DOC-Simplerest.txt). It remains unchanged as historical evidence.

## Quarantined legacy pages

Pages under [`pending/`](pending/README.md) are retained as audit inputs and are not part of the user documentation. In particular, `pending/framework/For-Reasoning-Architecture.md` is a proposal; its runtime profiles and command examples have not been verified as implemented SimpleRest features.
