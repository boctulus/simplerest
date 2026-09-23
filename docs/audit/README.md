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
| Every matching `WebRouter` route permits the later entry-point stages to run | A route that dispatches exits from `WebRouter::resolve()`. A package-specific `web_router` disable returns in the exact-alias branch or continues route search in the normal branch | Rejected | Separate dispatched-route short-circuit, package-disabled behavior, and no-match continuation |
| An unmatched HTTP request reaches `FrontController` when it is enabled | After a no-match return, `CliRouter::resolve()` returns immediately outside CLI; `index.php` then calls the enabled front controller | Verified for the checked-in switches and this no-match path | Keep other routing and deployment conditions explicit |
| Existing router tests establish entry-point order and process exit behavior | `WebRouterTest` covers registration/compilation/specificity; `WebRouterFunctionalTest` covers HTTP route outcomes, including a not-found case; neither asserts `index.php` resolver ordering or the process-level exit boundary | Unverified | Treat the entry-point behavior as source-traced, not test-covered |

## Legacy inventory: routing and request lifecycle

| Legacy source | Claims located for this phase | Disposition |
| --- | --- | --- |
| `docs/framework/Routing.md` → `pending/framework/Routing.md` | Combined WebRouter, CliRouter, package routing, front-controller handler, error, and request-flow descriptions | Preserved under `pending/framework/`; only the resolver and dispatch-flow claims below are promoted. Other router syntax, package, and CLI claims remain pending their phases |
| `docs/framework/WebRouter.md` → `pending/framework/WebRouter.md` | Route registration, groups, parameters, matching priority, and wildcards | Preserved under `pending/framework/`; only source-traced route compilation, matching, and short-circuit claims below are promoted |
| `docs/framework/FrontController.md` → `pending/framework/FrontController.md` | A fixed six-handler pipeline and configurable handler classes | Preserved under `pending/framework/`; handler instantiation and conditional execution are documented from source below |
| `docs/framework/Request.md` → `pending/framework/Request.md` | Query/body/route/header lookup, parsing, and immutable-method claims | Preserved under `pending/framework/`; this phase traces only request parsing in the front-controller lifecycle. The `Request` helper API remains pending |
| `docs/framework/Response.md` → `pending/framework/Response.md` | Output formatting, termination, and immutable-method claims | Preserved under `pending/framework/`; this phase traces only response finalization. Other output and immutable-method claims remain pending |

## Claim-level findings: routing and request lifecycle

| Claim | Evidence in this checkout | State | Documentation action |
| --- | --- | --- | --- |
| The checked-in configuration enables WebRouter, CliRouter, and FrontController and supplies six handler classes | `config/config.php` sets the three resolver switches to `true` and defines `front_behaviors` keys `request`, `api`, `auth`, `output`, `middleware`, `error` | Verified (checked-in configuration) | State that these are current defaults; customization and package overrides are separate claims |
| A resolved ordinary HTTP WebRouter route dispatches and prevents later `index.php` resolvers from running | `WebRouter::resolve()` matches the request method/path, dispatches a callback or controller, then exits on its ordinary route branch. The earlier exact-alias branch also exits after setting response data | Verified (source control flow) | Describe the matched-route short-circuit; do not claim `CliRouter` or `FrontController` also process that request |
| The exact named-route alias branch flushes the stored response before exiting | In `WebRouter::resolve()`, the exact-alias branch calls `Response::set()` and then `exit`; the adjacent `Response::flush()` call is commented out. `WebRouterTest::testCanRegisterRouteWithName()` asserts alias registration only, not alias resolution/output | Rejected as a source claim; user-visible impact remains untested | Record as a routing anomaly for separate investigation. Do not repair it or describe named-alias response behavior as verified |
| A WebRouter miss on HTTP continues to the front controller under the checked-in switches | A missing HTTP method bucket or no matching route returns from `WebRouter::resolve()`; `CliRouter::resolve()` returns outside CLI; `index.php` then calls enabled `FrontController::resolve()` | Verified for this path and configuration | Keep the no-match path separate from matched-route termination |
| WebRouter compilation sorts routes by specificity | `WebRouter::compile()` orders routes by wildcard presence, literal count, segment count, and parameter count. `WebRouterTest::testRoutesSortedBySpecificity()` asserts a literal route precedes a parameter route | Verified (implementation and test assertion) | Document only this ordering contract; the test source was inspected, not executed |
| Every request executes all six handler stages as a fixed pipeline | `FrontController::resolve()` instantiates the configured handlers, but dispatches auth, API, or ordinary controller resolution by branch; output runs only for non-null data; middleware runs after controller execution; error handling runs in the `Throwable` catch | Rejected | Replace the legacy fixed-pipeline claim with conditional stages and their actual order |
| `RequestHandler::parse()` separates HTTP and CLI inputs and classifies auth/API paths | For HTTP it parses `REQUEST_URI`, removes an `index.php` segment, adjusts `base_url`, and splits the path; for CLI it reads `$argv`. It returns params plus `is_auth` and `is_api` flags based on `remove_api_slug` and path segments | Verified (implementation path) | Document parsing/classification only; this does not verify every `Request` getter or API behavior |
| FrontController dispatch always reaches controller execution and response output | `FrontController::resolve()` returns on empty params or a disabled package front controller, validates class/method, invokes one resolved target, formats non-null output, runs middleware, flushes a non-empty response, and exits; `Throwable` is routed to `ErrorHandler::handle()` | Rejected as an unconditional claim; the listed branches are source-verified | Document the main path and its early returns/conditional work without implying every request traverses every stage |
| `Response::flush()` emits the prepared payload and terminates execution | `Response::flush()` chooses encoding/output based on state and request `Accept`, writes the body, then calls `exit`; WebRouter and FrontController call it only along their stated branches | Verified (source control flow) | Describe finalization only; format details and helper semantics remain pending |
| The existing router tests prove the full request lifecycle and currently pass | `WebRouterTest` asserts route registration, compiled patterns, and ordering. `WebRouterFunctionalTest` contains HTTP assertions for grouped routes, parameters, priority, and non-200 on a miss; it uses a configured running server. Neither covers `index.php` order, front-controller stage conditions, alias response flush, or process exits. `phpunit.xml` does not list `unit-tests/`; no tests were run | Rejected as a coverage/pass claim | State test assertions as source evidence only; retain execution status as unresolved under the PHPUnit finding |
| `Request::getOption()` source precedence and the full `Response` immutable-method contract are established by the lifecycle tests | `RequestImmutableMethodsTest` asserts clone independence for query parameters, headers, and body; `ResponseImmutableMethodsTest` asserts clone independence for status, headers, body, and JSON state. Those assertions do not test `getOption()` precedence or HTTP lifecycle dispatch | Unverified for the old helper claims | Keep these helper APIs in the pending pages for a later claim-level audit |

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

## Separate ACL investigation: `ApiController` callable tokens

| Claim or observation | Source and test evidence | State | Disposition |
| --- | --- | --- | --- |
| In the `$perms !== null` permission path, the POST case adds `get` when bit 4 is set | `ApiController::__construct()` calls `addCallable('get')` in its `POST` branch; the earlier special/resource-permission branch adds `post` | Verified (source observation) | Record the path and condition; do not infer intent from the nearby comment |
| In the same permission path, the PATCH case adds `putch` when bit 2 is set | `ApiController::__construct()` calls `addCallable('putch')`; the earlier special/resource-permission branch adds `patch` | Verified (source observation) | Record the path and condition; do not silently normalize the token |
| `get` and `putch` are intentional aliases for POST and PATCH, or confirmed defects | `Controller::addCallable()` stores exact strings and `FrontController::resolve()` checks the resolved request method against that list. `Collections` and `MySelf` use `post`/`patch`; no `ApiController`, `getCallable`, `putch`, or bitmask-callable test was found under `unit-tests/` | Unverified | Keep both as implementation anomalies for the authentication/ACL phase. No test or other code found here establishes intent, so neither is classified as a defect yet |

## Legacy inventory: installation and bootstrap

The following legacy material was inventoried for this phase. These pages are discovery inputs only; their text is not evidence for the claims below.

| Legacy source | Installation/bootstrap claims inventoried | Disposition |
| --- | --- | --- |
| [`pending/framework/QuickStart.md`](pending/framework/QuickStart.md), sections 1–4 and prerequisites | PHP version, database and web-server prerequisites; repository clone plus `composer install`; `composer require` and local-path Composer consumption; `.env.example` setup; root URL welcome-page check | Retained under `pending/` with this phase's claim findings added. Other API, database, auth, CLI, and routing content remains quarantined for its dependency-ordered phase |
| `pending/framework/Framework-Architecture.md` and `pending/framework/SimpleRest-Complete-Docs.md` | `app.php` described as the application bootstrap and repository tree presented as a standard application layout | Historical architecture descriptions; do not use as current setup instructions |
| `pending/framework/Release-Status.md` and `docs/to-do/Index.md` | PHP compatibility status and earlier claims about whether Composer installation exists | Historical snapshots; current Composer metadata below is the evidence for package identity and declared PHP constraint |

## Claim-level findings: installation and bootstrap

| Claim | Evidence in this checkout | State | Documentation action |
| --- | --- | --- | --- |
| Composer metadata identifies this project as `boctulus/simplerest`, type `library`, and declares PHP `>=8.1,<8.5` | Root `composer.json` package metadata and `require.php` constraint | Verified (metadata only) | State the declared package type and PHP constraint; do not turn metadata into a supported-app claim |
| `composer install` resolves the repository lock file in a clean source snapshot | `composer install --no-interaction --no-progress --quiet` exited 0 in a temporary `git archive` snapshot. The snapshot deliberately excluded `.env`, `.env.example`, `.dev-env`, and `config/databases.php`; 41 packages were installed. Output reported an unavailable `Google\Task\Composer` pre-autoload hook and PSR-4 warnings | Verified for dependency installation in that filtered snapshot; application startup remains unverified | Report only this scoped result. It was not an application bootstrap or HTTP smoke test |
| The documented local-path consumer example installs `boctulus/simplerest` with only the SimpleRest path repository configured | A clean consumer using that path repository and the `boctulus/simplerest @dev` constraint exited 2: Composer could not resolve required `boctulus/shopifyconnector @dev`. The SimpleRest manifest declares that dependency; its path repository entries are defined in the package manifest, not in the consumer test manifest | Rejected for the tested one-repository setup | Do not publish the legacy snippet as copy-and-run. A different consumer repository configuration or registry may change resolution and needs its own reproduction |
| `composer require boctulus/simplerest` from a public registry installs the current package | No public-registry installation was attempted; the local-path consumer attempt above failed on a transitive package | Unverified | Do not claim Packagist availability or successful public installation |
| A Composer consumer can use the package's `app.php` as its application bootstrap without additional setup | `app.php` resolves `vendor/autoload.php` and application paths relative to its own `__DIR__`; when that package-local autoloader is absent it changes into the package directory and invokes `composer install`. A normal Composer consumer's root autoloader is at the consumer root. No consumer bootstrap was run | Rejected as an implication of `composer require` alone; end-to-end consumer app use remains unverified | Document package metadata separately from a runnable consumer-app workflow |
| A clean clone runs as the repository's application after the legacy QuickStart steps | `index.php` requires root `app.php`; `app.php` loads `.env`, application helpers, `config/config.php`, and configured providers. No clean HTTP request was served. The reproduction snapshot omitted local environment/database configuration and the tracked `.env` was not read or copied | Unverified | Keep the application workflow out of runnable documentation until reproduced in a safe, clean app setup |
| The bootstrap can create `.env` from the checked-in `.env.example` automatically | `app.php` calls `Dotenv\Dotenv::createImmutable(__DIR__)->load()` when Dotenv is present. `Env::setup()` looks for `env.example` without the leading dot before attempting a copy; the repository template path is `.env.example`. No runtime test of this fallback was performed | Partial (source mismatch observed; runtime outcome untested) | Do not document automatic environment-file creation. The legacy manual-copy step remains unverified until exercised |
| The legacy prerequisites and “running in under five minutes” promise describe a verified install contract | `composer.json` provides a PHP platform constraint only. No clean database-backed or web-server workflow was reproduced; `.htaccess` contains rewrite rules, but no server configuration was run | Unverified for database/server prerequisites; rejected as a measured time claim | Defer DB requirements to the database phase and deployment requirements to deployment; omit the time promise |
| The root URL serves the promised welcome page after setup | `config/config.php` names `HomeController` as the default controller and `HomeController::index()` renders `home.php`; no HTTP request was run and no claim about a welcome response follows from class presence alone | Unverified | Do not present the old browser/curl check as verified |

### Reproduction scope and safety boundary

The repository's `.env` is tracked despite the ignore rule. Its contents were not read, copied, or changed. The clean Composer-install snapshot omitted it and local database configuration, so its successful dependency install does not reproduce the checkout's full application state. No PHPUnit suite, application bootstrap, HTTP request, database operation, or public Packagist install was executed in this phase.

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
| Six handler classes form an unconditional front-controller pipeline | `config/config.php` supplies six handler classes, but `FrontController::resolve()` invokes different resolution branches and conditionally calls output, middleware, and error handlers | Rejected as an unconditional pipeline; handler setup and branch order are split in the routing lifecycle findings | Document conditional execution and preserve the remaining handler customization claims as pending |
| Startup call order in `index.php` | `index.php` checks web router, CLI router, and front controller in that order; an earlier resolver can terminate the process before later checks | Verified for call order only | Document ordering separately from per-request execution |
| Composer dependency installation is sufficient to create an application | `composer.json` identifies a library package; the tested local-path consumer could not resolve `boctulus/shopifyconnector @dev`, and `app.php` resolves its autoloader/configuration paths relative to the package directory | Rejected for the tested one-repository local-path workflow; public-registry install and any consumer app bootstrap remain unverified | Keep library metadata separate from repository/application startup; do not publish the old Composer snippet as a runnable application setup |
| Performance claims such as 3–10 ms bootstrap | No benchmark procedure or reproducible environment is cited in the public overview | Rejected as an unqualified fact | Remove until measured with a documented, reproducible benchmark |
| “Multiple database engines supported” list | README lists engines, but the current driver and feature matrix has not been traced in this pass | Unverified | Audit each driver against connection and query implementation, then state exact limits |

## Scope and next audit sequence

Installation/bootstrap and routing/request lifecycle now have claim-level audits. Neither installation workflow is verified end to end; route-definition examples and request/response helper APIs remain pending. Continue in dependency order with database connections and Query Builder, then schemas and automatic API resolution; authentication and ACL; CLI and migrations; integrations; deployment; and performance. For each phase, capture implementation, configuration, tests, and runnable evidence before publishing a verified status.

The archived original is [`../framework/_archive/DOC-Simplerest.txt`](../framework/_archive/DOC-Simplerest.txt). It remains unchanged as historical evidence.

## Quarantined legacy pages

Pages under [`pending/`](pending/README.md) are retained as audit inputs and are not part of the user documentation. In particular, `pending/framework/For-Reasoning-Architecture.md` is a proposal; its runtime profiles and command examples have not been verified as implemented SimpleRest features.
