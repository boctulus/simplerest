# Getting started

A new application created from the published Composer release `v1.0.3` now reproduces installation, bootstrap, and a successful health request. A clean checkout of the current tracked repository installs dependencies but does not reach a successful HTTP response. Consuming the package as a Composer dependency remains unverified.

## Current workflow status

| Workflow | Status | Evidence |
| --- | --- | --- |
| Run the current tracked repository as an application | Partially reproduced; HTTP bootstrap blocked | `composer install` completed in a clean tracked archive. The first request failed on missing `.env`; after the Composer setup script generated it, route compilation failed because `DumbController` is absent from the clean checkout. |
| Create a new application from the published Composer release | Verified for `v1.0.3` | `composer create-project` completed, the setup hook created a sanitized `.env`, and `GET /system/health` returned HTTP 200 with `{"ok":true}`. This result is for the published release archive, not the current repository checkout. |
| Consume `boctulus/simplerest` as a Composer dependency | Unverified | Composer metadata declares a library package. A clean local-path consumer with only the SimpleRest repository configured could not resolve the required `boctulus/shopifyconnector @dev`; the public dependency-consumer bootstrap was not tested. |

## Clean installation and first HTTP request (2026-10-05)

On Windows with PHP 8.3.15 and Composer 2.8.5, the `composer create-project` command below exited successfully. It created `.env` through `post-root-package-install`, installed 40 locked packages, and emitted optimized-autoloader warnings for ambiguous classes and files that do not comply with the declared PSR-4 mappings. Starting PHP's built-in server and requesting `/system/health` returned HTTP 200, `Content-type:application/json;charset=utf-8`, and `{"ok":true}`.

The successful published-release flow was reproduced with these commands (run the request from a second terminal):

```powershell
composer create-project boctulus/simplerest my-simplerest 1.0.3 --no-interaction --prefer-dist --no-progress
Set-Location .\my-simplerest
php -S 127.0.0.1:18732 -t .
curl.exe -i http://127.0.0.1:18732/system/health
```

The repository checkout was tested separately from a clean archive of its tracked files at revision `1c1444526`. `composer install --no-interaction --prefer-dist --no-progress` exited successfully, but did not create `.env`. The first `GET /system/health` failed in `app.php` when `Dotenv::load()` tried to read that missing file. The PHP built-in server returned `HTTP/1.1 200` with a fatal error in the response body, so the status alone did not indicate a working application.

Running the registered `post-root-package-install` script explicitly with `composer run-script post-root-package-install` created `.env` from the tracked `.env.example` and cleared secret-like values. It did not unblock the request: the checked-in `config/routes.php` registers `DumbController`, which is absent from the clean tracked checkout and ignored by `.gitignore`. The next `GET /system/health` stopped during `WebRouter::compile()` with `Controller class Boctulus\Simplerest\Controllers\DumbController not found`; with the template environment the HTTP body was empty while the server logged the fatal error. The successful request above applies only to the published `v1.0.3` project archive.

There is a second environment-file mismatch: `Env::setup()` looks for `env.example`, while the tracked template is `.env.example`. In the `index.php` entry path, `app.php` calls `Dotenv::load()` before `Env::setup()`, so that fallback does not create the file needed by the bootstrap.

## Verified metadata and bootstrap paths

The root `composer.json` names the package `boctulus/simplerest`, declares type `library`, maps framework classes from `src/framework/`, and declares PHP `>=8.1,<8.5`. The published `v1.0.3` release works as a `create-project` application for the health request described above; that does not establish the current tracked checkout or a Composer dependency consumer as runnable.

For the repository application, `index.php` requires the root `app.php`. That bootstrap loads configuration and providers from the repository tree; the clean-checkout runtime failure is recorded above. In a Composer consumer, the same `app.php` computes paths relative to its package location and checks for a package-local `vendor/autoload.php`; if it is absent, it invokes Composer from inside that package. A consumer bootstrap was not run.

The Composer `post-root-package-install` helper can create a sanitized `.env` from `.env.example`, but a plain `composer install` does not call that event. Even after the helper runs, the missing `DumbController` prevents the checked-in web route table from compiling.

The tested commands, exact outcomes, implementation discrepancies, and unresolved prerequisites are in the [installation and bootstrap audit](../audit/README.md#claim-level-findings-installation-and-bootstrap). The former QuickStart remains quarantined as [legacy audit input](../audit/pending/framework/QuickStart.md); its API, database, authentication, routing, and CLI claims are still pending their ordered audit phases.
