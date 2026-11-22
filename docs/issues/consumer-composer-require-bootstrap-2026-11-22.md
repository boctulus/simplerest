# A new project installed with `composer require boctulus/simplerest` does not start

**Status:** ABIERTO. Items 2–8 are fixed in canonical `master` (commits `68caccfc2`, `c5c63b012`). The core fixes are released as `v3.0.6` (package commit `60286c9a8c82c52ee6089162f07b899fce4445f3`, on Packagist since 2026-11-22). The CLI-side fixes are in `simplerest-cli`, which is not published yet. Item 1 is mitigated by `simplerest-cli` (unpublished); item 9 and the ACL gap (item 10) remain open.

**Confidence:** items 1–6 and 8 are confirmed in source at canonical commit `cf5af51c5`. Item 7 was confirmed only in part. The incident timeline comes from the maintainer's report and the consuming project's notes; it was not reproduced step by step.

## Incident (Contextual)

On 2026-11-22, the maintainer created the project `D:\laragon\www\quality` (Composer name `boctulus/quality-control`) with `composer require boctulus/simplerest`. Packagist resolved that requirement to core `3.0.5`, published the same day (package commit `0d2dd76c40675c0a84d1fcf0a28dc01462f38e29`). The project then built a REST MVP with seven resources.

The first `php com`, which had no `com` file, failed with:

```text
PS D:\laragon\www\quality> php com
Could not open input file: com
```

After `com`, `app.php` and `config/` were copied from the development monorepo, it failed with:

```text
Fatal error: Uncaught UnexpectedValueException: DirectoryIterator::__construct(D:\laragon\www\quality\config/../src/framework/Helpers): El sistema no puede encontrar la ruta especifica (code: 3) in D:\laragon\www\quality\app.php:105
Stack trace:
#0 D:\laragon\www\quality\app.php(105): DirectoryIterator->__construct('D:\\laragon\\www\\...')
#1 D:\laragon\www\quality\com(17): require_once('D:\\laragon\\www\\...')
#2 {main}
  thrown in D:\laragon\www\quality\app.php on line 105
```

The project had none of the following: `com`, `.env`, configuration files, basic framework documentation, the base packages (CLI, i18n, ACL, JWT authentication, scaffolding), `.htaccess`, `MyModel`, `MyAuthController`, `UsersModel`, or the base migrations (`migrations`, `users`, `roles`, `api_keys`, `webhooks`, …). They were copied by hand from `D:\laragon\www\simplerest`. The maintainer also reports that at one point a directory with "monorepo" in its name appeared in the wrong place: its contents belonged at the project root. Its exact name and origin were not recorded. The project's `app/Commands/` also received the monorepo-only groups `doc`, `pack`, `skill` and `todo`.

The project now requires these packages through `path` repositories that point to `../simplerest/packages/boctulus/*` with `symlink: true`, and it registers a shim namespace in `composer.json`:

```json
"Boctulus\\Simplerest\\Core\\Libs\\Impersonation\\": "app/Shims/Impersonation/"
```

The AI agent that implemented the MVP estimated that about half of its time went to the items below. It also listed what worked well: `php com make any all --schema --model --api` generated CRUD for all seven resources with filters, operators, pagination, sorting and `fields`; API-key authentication (`api_keys`, `X-API-KEY`, ACL roles) worked out of the box; the `ApiController` hooks (`onPostingBeforeCheck`, `onPost`, …) held the domain rules; and `php com sql`, `php com acl` and `php com users` replaced ad-hoc scripts. The consuming project's own notes record items 1–6 below in `D:\laragon\www\quality\docs\to-do\plan.md`.

## Root Cause (Abstracted)

| # | Defect | Owner | Evidence | Status |
| --- | --- | --- | --- | --- |
| 1 | The core is a library (`src/framework` only, [PACK contract](../architecture/pack-contract.md)). There is no supported way to get the application bootstrap (`com`, `app.php`, `index.php`, `config/`, `.env`), and the bootstrap copied from the monorepo hard-codes monorepo paths (`config/autoload.php` → `../src/framework/Helpers`), providers and database connections. Composer does not run a dependency's scripts, so the core cannot generate these files. | distribution | `config/autoload.php`, `config/config.php` (`providers`) in the monorepo | Mitigated: `vendor/bin/simplerest init` in [`simplerest-cli`](../packages/cli.md). Not published yet. |
| 2 | The application base pieces are not distributed anywhere: `.htaccess`, `app/Models/MyModel.php`, `app/Controllers/MyAuthController.php`, `app/Models/main/UsersModel.php` and the base migrations (`database/migrations/0000_*`, `2021_09_*`: `migrations`, `users`, `roles`, `user_roles`, permissions, `api_keys`, …). | distribution | These files exist only in the monorepo | Fixed in `init`: `.htaccess` (without `php_value`), `MyModel`, a hooks-only `MyAuthController`, `UsersModel`, `HomeController` + `home.php`, layout `templates/tpl_basic.php` (the default of `WebController`), and the 15 base migrations that `quality` used. In an empty database `php com migrations migrate` creates `api_keys`, `migrations`, `roles`, `sp_permissions`, `user_deny_permissions`, `user_roles`, `user_sp_permissions`, `user_tb_permissions`, `users`, `webhooks`. |
| 3 | `ApiController` imports and calls `Core\Libs\Impersonation\ImpersonationRequestContext` and `ImpersonationManager`. No file in the repository defines them. Their comment ("hiding buttons in the Svelte UI", `/api/v1/properties/1`) describes another application, which suggests consumer code that reached the canonical core. Every request handled by `ApiController::__construct()` reaches line 213 and fails without a shim. | core | `src/framework/Api/ApiController.php:14-15, 213-220`; the import has been present since `a12825507` | Fixed: the imports and the read-only impersonation call were removed from `ApiController`; `impersonated_by` is still read from the token. Applications that need read-only impersonation enforce it in their own controller. New guard: `unit-tests/microcore/CoreReferencesResolveTest.php` fails when the core imports a core class or calls a function that the core does not ship. It also found and fixed: `tag()` helper (moved to `simplerest-bootstrap`), `gdrive.php` helpers (moved to `simplerest-gdrive`), broken `get_api_name()` (removed; it called an undefined `get_api_namespace()`), unused imports of missing classes in `FileUploader`, `Update`, `Utils`. Published `v3.0.5` still has the defect. |
| 4 | The query builder does not quote column identifiers in `WHERE`. A reserved-word column such as `key` breaks `?key=`. | core | `src/framework/Traits/QueryBuilderTrait.php` `_where()`: `"$field $op ..."` | Fixed, and extended: the consumer test showed the same defect in `INSERT` column lists, `UPDATE ... SET`, `SELECT`/`GROUP BY` lists and paginator `ORDER BY`; all now quote plain identifiers ([query builder](../database/query-builder.md#identifier-quoting)). |
| 5 | `Schema::default($val)` stores the value verbatim, and the DDL emits `DEFAULT {$value}`. String literals need manual quotes: `default("'texto'")`. | core | `src/framework/Libs/Schema.php:1029-1036, 1269-1270` | Fixed: `Schema::defaultLiteral()` quotes string literals and keeps numbers, quoted values, keywords and function calls ([schema builder](../database/schema-builder.md)). |
| 6 | `make migration <table> --create` leaves the `__TB_...` placeholder unresolved in the generated `$table`. The template uses `__TB_NAME__` and the generator replaces it in one path (`BaseMakeCommand.php:2333`); the `--create` path was not traced. | scaffolding | `src/framework/Templates/Migration*.php:11`; `simplerest-scaffolding/src/Commands/BaseMakeCommand.php:2333` | Fixed. Root cause: `NAME__` was replaced before `__TB_NAME__`, and `NAME__` is a substring of `__TB_NAME__`; the `--class` option had the same collision. Verified: `make migration gadgets --create` → `$table = 'gadgets'`. The reported lowercase namespace also came from the generator: `controller`, `console`, `middleware` and `task` emitted `\controllers`, `\middlewares`, `\jobs\tasks`; they now emit `Controllers`, `Middlewares`, `Background\Tasks`, which match the PSR-4 directories (`page` keeps the `pages` namespace that `PagesTrait` resolves). |
| 7 | Errors in routed controllers are not visible. `WebRouter::resolve()` calls the controller, stores the return value with `$res->set($data)` and calls `exit`; the explicit `flush()` is commented out, and there is no `try/catch`. The consumer saw an empty HTTP 200 for an exception (a missing `use`), and `response()->send()` emitted nothing, so it used `sendJson()->flush()`. Whether the empty 200 comes from `WebRouter` or from `display_errors`/`error_reporting(0)` when `debug` is false was not determined. | core | `src/framework/WebRouter.php:153-162` | Fixed ([web routing](../core/web-routing.md)): exceptions are logged and answered with HTTP 500, a `null` return keeps `response()->send()` data, and the alias branch flushes. Verified over HTTP: a throwing controller returns 500 with the message and location when `debug` is on, and "Internal server error" when it is off; `response()->send()` returns HTTP 200 with the data. |
| 8 | The core calls `trans()`, which is defined only by `boctulus/simplerest-i18n`. | core | [separate issue](core-calls-i18n-trans-without-dependency.md) | Fixed in the core: `Helpers/trans.php` defines a pass-through `trans()` when `simplerest-i18n` is absent, and `Validator` binds its text domain only when `Translate` exists. |
| 9 | The framework is not well represented in AI training data, so an agent has to read the source for almost every API. The installed project received no framework documentation or skills. | distribution / docs | consumer report | Planned: `publish docs` / `publish skills` (step 3 of the plan below) |
| 10 | A public consumer cannot get a working API: `Core\Security\Acl` is abstract, `Factory::acl()` requires `config/acl.php`, and the only concrete ACLs are `simplerest-basic-acl` and `simplerest-fine-grained-acl`, which are recorded as private packages. Without `config/acl.php` the core used to fail with a type error. | core / distribution | `src/framework/Security/Acl.php` (abstract), `src/framework/Libs/Factory.php` | Diagnostic fixed: `Factory::acl()` now names the missing file and what it must return; `init` ships `config/acl.php.example`. Open: a public ACL implementation, or a decision to publish one of the ACL packages (maintainer decision). |
| 11 | The documentation governance guard hung when Git wrote many CRLF warnings to stderr. | tooling | [separate issue](documentation-guard-deadlock-on-git-stderr.md) | Fixed with maintainer approval |

Items 1, 2 and 9 together explain the "missing pieces" cycles. Item 7 explains the most expensive debugging round. Items 3–6 are defects that every consumer hits regardless of how the project was created.

## Reusable Fix (Sanitized)

Applies to any project that consumes the SimpleRest core through Composer.

1. **Bootstrap:** never copy `com`, `app.php` or `config/` from a development checkout. Install `boctulus/simplerest-cli` and run `vendor/bin/simplerest init`. It writes portable stubs where `CORE_PATH` is resolved through `Composer\InstalledVersions::getInstallPath('boctulus/simplerest')`, and it never overwrites existing files.
2. **Base application pieces (item 2):** extend `init` (or the future `boctulus/simplerest-skeleton`) to ship `.htaccess`, `MyModel`, `MyAuthController`, `UsersModel` and the base migrations as stubs, and test that `php com migrations migrate` creates the base tables in an empty database.
3. **Core defects (items 3–7):** fix each one in `src/framework` with a regression test, then release a new core version:
   - item 3: remove the application-specific impersonation calls from `ApiController`, or make impersonation a declared extension point owned by a package (owner decision);
   - item 4: quote identifiers in `WHERE` with the driver's quoting;
   - item 5: quote string defaults in `Schema::default()`, or document and validate the raw-SQL contract;
   - item 6: resolve the placeholder on every `make migration` path;
   - item 7: let exceptions from routed controllers reach the configured error handler with a non-200 status, and flush the response explicitly.
4. **Documentation for agents (item 9):** publish the curated framework documentation and skills into the project (`publish docs`, `publish skills`), so that an agent does not need to read the source.

Until items 3–5 are fixed, a consuming project can use these workarounds: a shim for the missing impersonation classes, column names that are not reserved words, quoted string defaults (`default("'value'")`), and `sendJson()->flush()` in routed controllers. They are workarounds, not supported APIs.

## Portable Validation

In a new, empty directory:

```powershell
composer init --no-interaction --name=example/app
composer require boctulus/simplerest boctulus/simplerest-cli boctulus/simplerest-scaffolding
php vendor/bin/simplerest init
php com
php com migrations migrate        # item 2: base tables are created
php -S 127.0.0.1:8000 index.php   # GET /system/health → 200; a controller that throws → non-200 with the error logged (item 7)
```

`composer show boctulus/simplerest` must not need any shim under `Core\Libs\Impersonation` (item 3). Regression tests for items 4–6 belong to the core and scaffolding test suites.

## Acceptance run (2026-11-22)

A new consumer project was built with the core copied from `src/framework` of the working tree (as version `3.0.6`), plus `simplerest-cli`, `-i18n`, `-scaffolding`, `-jwt-auth` and `-fine-grained-acl` as local `path` repositories. The database was a new disposable MySQL schema, `sr_accept_test_20261122`. PHP 8.3.36, Composer 2.8.5, Windows 11.

- `vendor/bin/simplerest init` ran, and `php com` listed 12 groups on the first run.
- `php com migrations migrate` created the 10 base tables, and `php com make any all --schema --model --api` generated their schemas, models and API controllers. It kept the shipped `UsersModel` unchanged.
- `make migration gadgets --create` resolved `$table`. A `key varchar` column with `default('abc')` and a `size` column with `default('Large')` produced valid DDL. Insert, `where(['key', ...])` and update on `key` worked.
- Over HTTP (`php -S`):
  - `GET /` and `GET /system/health` returned 200.
  - `GET /api/v1/gadgets?key=k2` and `?fields=id,key&orderBy[key]=DESC&size=5` returned 200 with data and paginator.
  - A throwing controller returned 500, and `response()->send()` returned 200.
- Not verified: API-key and JWT authentication flows, and acceptance criteria on Linux.

The consuming project's own write-up of this incident is `D:\laragon\www\quality\docsramework\SimpleRest-Composer-Integration-Findings.md`. It adds the acceptance criteria used above and the report of a misplaced "monorepo" directory; that directory's origin was not identified.

## Related

- Plan: [`docs/to-do/consumer-cli-skeleton-laravel-style.md`](../to-do/consumer-cli-skeleton-laravel-style.md)
- Package: [CLI](../packages/cli.md)
- Release process: [Composer release process](../architecture/composer-release-process.md)
