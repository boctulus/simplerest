# SimpleRest

SimpleRest is a PHP framework whose Composer core is a small HTTP kernel. This monorepo also contains the legacy application, extension packages, tests, and an archive of code removed from the kernel while those capabilities are migrated or retired.

The framework has its own runtime and conventions. Its behavior is defined by the code in this repository; similarities to other PHP frameworks are not a substitute for the SimpleRest API or architecture.

## Repository at a glance

- `src/framework/` — the HTTP request/response, router, generic middleware pipeline, dependency container, and JSON front controller.
- `app/` — application controllers, models, commands, schemas, views, and modules used by this checkout.
- `config/` — application configuration and route registration.
- `database/` — database migration files used by this application.
- `unit-tests/` — repository PHPUnit tests, selected by `composer test` and listed in the current `phpunit.xml` suite.
- `examples/` — example code.
- `packages/` — packages maintained in this repository.
- `docs/` — topic documentation and the documentation audit record.

`app.php`, `index.php`, and `config/` still describe the legacy monolithic application bootstrap. They are not included in the Composer core artifact and have not yet been migrated to the microcore API.

## Framework and extensions

The microcore provides HTTP request parsing, route matching, generic middleware, dependency resolution, and JSON responses. It has no database, authentication, ACL, HTML, CLI, or external API implementation. The repository's existing extension packages remain separate from the core; their current tags predate this microcore boundary and have not yet been validated against it:

| Composer package | What it provides |
| --- | --- |
| [`boctulus/simplerest-scaffolding`](https://github.com/boctulus/simplerest-scaffolding) | `make` commands, code generators, and PHP development tools |
| [`boctulus/simplerest-bootstrap-html`](https://github.com/boctulus/simplerest-bootstrap-html) | Bootstrap 5 forms, AdminLTE components, and Bootstrap pagination |
| [`boctulus/simplerest-fpdf`](https://github.com/boctulus/simplerest-fpdf) | `PdfBase`, a SimpleRest adapter around FPDF |
| [`boctulus/simplerest-web-inspection`](https://github.com/boctulus/simplerest-web-inspection) | CMS and technology detection plus HTML selector helpers |
| [`boctulus/simplerest-api-clients`](https://github.com/boctulus/simplerest-api-clients) | Google Drive, WooCommerce, OneSignal, and Hugging Face clients |
| [`boctulus/simplerest-llm-providers`](https://github.com/boctulus/llm-providers) | Unified OpenAI, Anthropic, xAI, and Ollama provider interface |

The public extension tags and repositories were not changed by this refactor. New compatible extension versions and their migration guides remain pending. The BasicACL and FineGrainedACL extensions are proprietary and are not part of the public Composer catalogue.

## Requirements

The public `composer.json` requires only PHP `>=8.1,<8.5` and `doctrine/inflector` `^2.0` (used to name pivot subresources). A microcore installation does not require PDO, a database driver, JWT, PSR-7, Bootstrap, or SDK packages. Applications that install extensions must also satisfy those extensions' own requirements.

## Microcore usage

The 3.x microcore work is not tagged or published yet. Once a compatible 3.x release is available, a consumer can register a route and send its JSON response without application configuration or extensions:

```php
require __DIR__ . '/vendor/autoload.php';

use Boctulus\Simplerest\Core\FrontController;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\WebRouter;

WebRouter::get('/health', static fn (Request $request): array => ['status' => 'ok']);
FrontController::resolve();
```

## Composer pack workflow

The development checkout is not automatically the public Composer distribution. Build the curated microcore with `php com pack build --profile=core` (or add `--dry-run`); the destination defaults to the supported sibling `simplerest-pack` Git worktree. A default build rejects uncommitted changes under `composer.json`, `LICENSE`, `README.md`, or `src/framework/`, while an explicit `--source-commit=<sha>` reproduces a deliberate immutable revision. Dirty, linked, overlapping, unversioned, or non-core existing destinations are rejected without deletion.

VERIFY builds a standalone snapshot **only from the curated output** with `scripts/composer/build-core-source-snapshot.ps1 -CuratedDirectory <pack-path> -OutputDirectory <new-path> -SourceCommit <source-sha>`. It emits external provenance, and `scripts/composer/verify-installed-core.php` compares an installed Composer candidate to those hashes. Packagist serves the Git commit registered for a version, not the local PACK directory; therefore PACK, VERIFY, PUBLISH, and application upgrades remain separate operations. See the [canonical PACK contract](docs/architecture/pack-contract.md). None of these commands publishes or updates GoProp automatically.

The historical application bootstrap is not a microcore consumer. Its original classes are explicitly loaded for the development application outside the Composer autoload while that application is migrated. Full legacy CLI/application compatibility has not yet been certified.

## Getting started

This checkout is an application repository as well as framework source. The Composer release contains the microcore only; it does not copy the legacy app bootstrap or automatically install extension code. The extraction archive in `archive/` is excluded from Composer artifacts and is not an autoload source.

Start with the [canonical documentation map](docs/index.md). The [audit register](docs/audit/README.md) is internal; topic pages not linked from the map are legacy or pending audit input, not current specifications.

The [official Spanish documentation](https://boctulus.github.io/simplerest/) is published with VitePress from [`docs-site/`](docs-site/); it is a derivative, not the canonical source. See [documentation publishing](.github/DOCS-PUBLISHING.md) for build checks and publication criteria.

## Development checks

The monorepo keeps PHPUnit outside the public package manifest. In PowerShell, install its development dependencies and run the microcore checks with:

```powershell
$env:COMPOSER = 'composer-dev.json'
composer install
composer test:microcore
```

The legacy application suite still depends on migration work and is not represented by `test:microcore`.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

SimpleRest is distributed under the MIT License. See [LICENSE](LICENSE).
