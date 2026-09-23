# Getting started

Neither supported installation workflow has been reproduced end to end. This page records what the current repository establishes; it is not a copy-and-run setup guide.

## Current workflow status

| Workflow | Status | Evidence |
| --- | --- | --- |
| Run this repository as an application | Unverified | `composer install` completed in a filtered clean source snapshot, but the application bootstrap and an HTTP request were not run. |
| Consume `boctulus/simplerest` as a Composer dependency | Unverified | Composer metadata declares a library package. A clean local-path consumer with only the SimpleRest repository configured could not resolve the required `boctulus/shopifyconnector @dev`; public-registry installation and application bootstrap were not tested. |

## Verified metadata and bootstrap paths

The root `composer.json` names the package `boctulus/simplerest`, declares type `library`, maps framework classes from `src/framework/`, and declares PHP `>=8.1,<8.5`. These are Composer metadata facts; they do not establish a runnable consumer application.

For the repository application, `index.php` requires the root `app.php`. That bootstrap loads configuration and providers from the repository tree. In a Composer consumer, the same `app.php` computes paths relative to its package location and checks for a package-local `vendor/autoload.php`; if it is absent, it invokes Composer from inside that package. This path was inspected in source, but a consumer bootstrap was not run.

The environment-file setup also needs correction or reproduction before it can be documented as an install step: `app.php` calls `Dotenv::load()` for `.env`, while `Env::setup()` looks for `env.example` without the leading dot when it tries to create a missing `.env`. No automatic environment-file creation is documented here.

The tested commands, exact outcomes, implementation discrepancies, and unresolved prerequisites are in the [installation and bootstrap audit](../audit/README.md#claim-level-findings-installation-and-bootstrap). The former QuickStart remains quarantined as [legacy audit input](../audit/pending/framework/QuickStart.md); its API, database, authentication, routing, and CLI claims are still pending their ordered audit phases.
