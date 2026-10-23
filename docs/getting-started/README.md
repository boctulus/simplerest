# Getting started

A new application created from the published Composer release `v1.0.3` reproduces installation, bootstrap, and a successful health request. A fresh Windows clone of branch `docu` at `f6d74e69` also completed Composer setup and served both `/system/health` and a custom `/hello` route. This verifies the minimal HTTP flow only; no database-backed CRUD or Composer dependency consumer was tested.

## Current workflow status

| Workflow | Status | Evidence |
| --- | --- | --- |
| Run the current tracked repository as an application | Verified for a minimal HTTP flow on Windows | A fresh clone of `docu` at `f6d74e69` had no case-colliding paths. Composer installed 40 packages; the setup hook created a sanitized `.env`; `GET /system/health` and a locally added `GET /hello` each returned HTTP 200. The smoke used dummy loopback DB/mail settings and did not connect to a database. |
| Create a new application from the published Composer release | Verified for `v1.0.3` | `composer create-project` completed, the setup hook created a sanitized `.env`, and `GET /system/health` returned HTTP 200 with `{"ok":true}`. This result is for the published release archive, not the current repository checkout. |
| Consume `boctulus/simplerest` as a Composer dependency | Unverified | Composer metadata declares a library package. A clean local-path consumer with only the SimpleRest repository configured could not resolve the required `boctulus/shopifyconnector @dev`; the public dependency-consumer bootstrap was not tested. |

## Clean installation and first HTTP request (2026-10-22)

On Windows with PHP 8.3.36 and Composer 2.8.5, a fresh clone of `docu` at `f6d74e69` completed checkout, installed 40 packages, and generated `.env` through `post-root-package-install`. The release `v1.0.3` was also installed with `composer create-project`. Both workflows returned HTTP 200 from `/system/health`. Composer reported ambiguous classes and PSR-4 mapping warnings; those warnings did not prevent the two HTTP requests tested here.

The successful published-release flow was reproduced with these commands (run the request from a second terminal):

```powershell
composer create-project boctulus/simplerest my-simplerest 1.0.3 --no-interaction --prefer-dist --no-progress
Set-Location .\my-simplerest
php -S 127.0.0.1:18732 index.php
curl.exe -i http://127.0.0.1:18732/system/health
```

## Add and run a first endpoint

Add a route beside the other registrations in `config/routes.php`:

```php
WebRouter::get('hello', function () {
    return ['message' => 'Hello from SimpleRest'];
});
```

From the project root, run PHP's built-in server with `index.php` as its router, then request both endpoints:

```powershell
php -S 127.0.0.1:18789 index.php
```

```powershell
curl.exe -i http://127.0.0.1:18789/system/health
curl.exe -i http://127.0.0.1:18789/hello
```

In the smoke run, both returned HTTP 200; `/system/health` returned `{"ok":true}` and `/hello` returned `{"message":"Hello from SimpleRest"}`. This route does not use a database.

## Current source clone (2026-10-22)

A fresh Windows clone of branch `docu` at `f6d74e69` checked out 9,660 tracked files with zero case-colliding paths. `composer install --no-interaction --prefer-dist --no-progress` installed 40 packages. Running `composer run-script post-root-package-install` created a sanitized `.env`. With dummy database and mail hosts at `127.0.0.1:1`, `/system/health` and the route above both returned HTTP 200. No database connection was opened. Composer still emitted ambiguity and PSR-4 warnings during optimized autoload generation.

## Historical bootstrap failure (revision `1c1444526`)

An earlier clean archive of the tracked files at `1c1444526` installed dependencies but did not create `.env`. The first `GET /system/health` failed in `app.php` when `Dotenv::load()` tried to read that missing file. The PHP built-in server returned HTTP 200 with a fatal error in the body, so the status alone did not indicate a working application.

Running the registered `post-root-package-install` script explicitly with `composer run-script post-root-package-install` created `.env` from the tracked `.env.example` and cleared secret-like values. In revision `1c1444526`, the next `GET /system/health` stopped during `WebRouter::compile()` with `Controller class Boctulus\Simplerest\Controllers\DumbController not found`; the built-in server logged the fatal error. That revision had active `DumbController` routes in `config/routes.php`. Those examples were commented out by `0c7ef58`. The later `f6d74e69` clone completed the post-helper startup and the minimal HTTP smoke above.

There is a second environment-file mismatch: `Env::setup()` looks for `env.example`, while the tracked template is `.env.example`. In the `index.php` entry path, `app.php` calls `Dotenv::load()` before `Env::setup()`, so that fallback does not create the file needed by the bootstrap.

## Verified metadata and bootstrap paths

The root `composer.json` names the package `boctulus/simplerest`, declares type `library`, maps framework classes from `src/framework/`, and declares PHP `>=8.1,<8.5`. The published `v1.0.3` release works as a `create-project` application for the health request described above. The current source checkout has its own minimal HTTP reproduction; neither result establishes a Composer dependency consumer or database-backed CRUD workflow.

For the repository application, `index.php` requires the root `app.php`. In the clean Windows clone at `f6d74e69`, that bootstrap served the health route and the custom route after Composer installed dependencies and the setup hook created `.env`. In a Composer consumer, the same `app.php` computes paths relative to its package location and checks for a package-local `vendor/autoload.php`; if it is absent, it invokes Composer from inside that package. A consumer bootstrap was not run.

The Composer `post-root-package-install` helper can create a sanitized `.env` from `.env.example`, but a plain `composer install` does not call that event. The `DumbController` compilation failure was reproduced only with revision `1c1444526`; a clean clone at `f6d74e69` passed the post-helper startup and minimal HTTP smoke. A Composer dependency consumer and database-backed CRUD remain unverified.

The tested commands, exact outcomes, implementation discrepancies, and unresolved prerequisites are in the [installation and bootstrap audit](../audit/README.md#claim-level-findings-installation-and-bootstrap). The former QuickStart remains quarantined as [legacy audit input](../audit/pending/framework/QuickStart.md); its API, database, authentication, routing, and CLI claims are still pending their ordered audit phases.
