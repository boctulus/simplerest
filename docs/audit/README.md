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

## Legacy claim inventory: schema-generated and automatic API

These legacy pages were inspected to locate claims. Their prose is not evidence for any status below; the findings are based on current source, configuration, and test assertions.

| Legacy source | Claim located | Disposition |
| --- | --- | --- |
| `pending/framework/QuickStart.md` | Running `make schema products` alone makes CRUD ready; examples call `/api/products` | The schema-alone claim and unversioned path are rejected; other setup and authentication instructions remain pending |
| `pending/framework/Schemas.md` | A schema automatically creates a complete REST endpoint | Rejected; remaining schema claims stay pending |
| `framework/Release-Status.md` | Auto REST endpoints are complete, with zero-config CRUD from table names and advanced filtering | Zero-config CRUD is rejected; advanced filtering remains unverified; the status snapshot is quarantined |
| `framework/ORM-STATUS.md` | References automatic endpoints as current feature documentation | Quarantined; the link text is not implementation evidence |
| `pending/framework/AutomaticEndpoints-Summary.md` | Describes API routing and endpoint coverage | Remains pending; this pass verifies only the resolver mapping below, not the full endpoint contract |

## Claim-level findings: schema generation and automatic API

| Claim | Source trace | State | Test and runtime boundary |
| --- | --- | --- | --- |
| `make schema <table>` generates a schema file | `MakeSchemaCommand::execute()` calls `BaseMakeCommand::schema()`, which checks the selected connection for the table, renders `SCHEMA_TEMPLATE`, and writes a `*Schema.php` file | Verified (implementation path) | Requires a usable configured/default DB connection and existing table. No generator command test was found or run |
| `make model <table>` generates a model file | `MakeModelCommand::execute()` calls `BaseMakeCommand::model()`, which renders a model template and writes under `MODELS_PATH`; `--no-schema` selects the schema-less template | Verified (implementation path) | Connection/schema conditions vary with options. No generator command test was found or run |
| `make api <table>` generates an API controller file | `MakeApiCommand::execute()` calls `BaseMakeCommand::api()`, which renders `API_TEMPLATE` (`ApiRestfulController.php`) and writes under `API_PATH` | Verified (implementation path) | This establishes file-generation code, not that the output resolves or serves requests. No generator command test was found or run |
| `make any <table> --schema --model --api` can request all three generator calls | `MakeAnyCommand::config()` declares all three flags; `BaseMakeCommand::any()` checks each flag and calls `schema()`, `model()`, and `api()` in sequence | Verified (dispatch path) | This does not verify generated artifacts or an API request. The command was not run |
| A normal API resource slug resolves to an application API-controller class name | `ApiHandler::resolve()` reserves `trashcan` and `collections`; other slugs are converted to PascalCase under `namespace_url() . '\\Controllers\\api\\'` and paired with the request method | Verified (resolver mapping) | Resolution returns a class name; class presence and successful execution are separate checks |
| The front controller validates the resolved API target before dispatch | `FrontController::resolve()` checks `class_exists`, checks `method_exists` (allowing `__call` under its stated exception), then requires the method in `getCallable()` for non-auth API requests before calling it | Verified (source path) | Does not prove authorization succeeds for a particular controller, schema, DB, or request |
| `php com make schema products` alone makes a complete CRUD API ready | That command dispatches only `BaseMakeCommand::schema()`; the API resolver selects a controller class and `FrontController` validates its target and method allowlist. The current checkout already contains `ProductsSchema`, `ProductsModel`, and `Controllers\\api\\Products`; their presence is not an effect established by this command | Rejected | The old claim in `pending/framework/QuickStart.md` is annotated and the old schema guide is quarantined. No complete workflow is implied |
| A schema alone makes a complete REST API available for every table | `ApiHandler::resolve()` maps ordinary resource names to application API-controller class names; `FrontController::resolve()` checks the resolved class, method, and API callable list. No schema scan or controller creation occurs in this request path | Rejected | This is the automatic-endpoint claim from the legacy schema guide |
| Automatic endpoints provide advanced filtering | Related API tests contain filter assertions, but no relevant test was run and the behavior depends on the database and Query Builder path | Unverified | Audit with database connections and Query Builder; the old release-status snapshot remains pending |
| The QuickStart sample path `/api/products` is valid under the checked-in API configuration | `config/config.php` sets `remove_api_slug` to `false`; `ApiHandler::resolve()` requires the next path parameter to match a `vN` version | Rejected | The sample omits the required version segment. Existing API tests use `/api/v1/products`; they do not establish the old sample path |
| A clean, end-to-end generator-to-CRUD workflow works | `ProductsBasicTest` asserts GET response/data behavior; `ProductsPaginationTest` asserts pagination; `ApiTest::testNullOperator()` contains POST, GET, and DELETE requests against an already-running app and database. These tests do not invoke the generators. `phpunit.xml` does not include `unit-tests/` | Unverified | No generator-to-request workflow was run in a controlled setup; existing test source is not a test result |

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
| Composer dependency installation is sufficient to create an application | `composer.json` identifies a library package and has core PSR-4 mappings; the repository bootstrap also loads application/configuration paths | Unverified | Keep library consumption and repository/application installation separate; validate both independently |
| Performance claims such as 3–10 ms bootstrap | No benchmark procedure or reproducible environment is cited in the public overview | Rejected as an unqualified fact | Remove until measured with a documented, reproducible benchmark |
| “Multiple database engines supported” list | README lists engines, but the current driver and feature matrix has not been traced in this pass | Unverified | Audit each driver against connection and query implementation, then state exact limits |

## Scope and next audit sequence

This is the initial baseline, not a completed audit of every topic. Next, audit in dependency order: clean installation and bootstrap; routing/request lifecycle; database connections and query behavior; schemas and automatic API resolution; authentication and ACL; CLI and migrations; then optional integrations, views, deployment, and performance. For each page, capture implementation, configuration, tests, and runnable evidence before publishing a verified status.

The archived original is [`../framework/_archive/DOC-Simplerest.txt`](../framework/_archive/DOC-Simplerest.txt). It remains unchanged as historical evidence.

## Quarantined legacy pages

Pages under [`pending/`](pending/README.md) are retained as audit inputs and are not part of the user documentation. In particular, `pending/framework/For-Reasoning-Architecture.md` is a proposal; its runtime profiles and command examples have not been verified as implemented SimpleRest features.
