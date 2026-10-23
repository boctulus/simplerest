# SimpleRest

SimpleRest is a PHP framework for building HTTP applications and REST APIs. This repository contains the framework source, an application bootstrap, configuration and routing files, a command-line entry point, tests, examples, and locally developed packages.

The framework has its own runtime and conventions. Its behavior is defined by the code in this repository; similarities to other PHP frameworks are not a substitute for the SimpleRest API or architecture.

## Repository at a glance

- `src/framework/` — framework classes, including the HTTP and CLI routers, request pipeline, API base classes, database utilities, and other core components.
- `app/` — application controllers, models, commands, schemas, views, and modules used by this checkout.
- `config/` — application configuration and route registration.
- `database/` — database migration files used by this application.
- `unit-tests/` — repository PHPUnit tests, selected by `composer test` and listed in the current `phpunit.xml` suite.
- `examples/` — example code.
- `packages/` — packages maintained in this repository.
- `docs/` — topic documentation and the documentation audit record.

`index.php` loads `app.php`, then checks the web router, CLI router, and front controller in `config/config.php` order. A web route that matches and dispatches exits before later stages. The front controller builds its request-processing handlers from `front_behaviors`. See [Architecture](docs/architecture.md) for the evidence-backed overview.

## Framework and extensions

SimpleRest provides automatic REST endpoints, routing, a database Query
Builder, authentication hooks, and configurable ACL support. Applications can
add focused extensions for common development and integration tasks:

| Composer package | What it provides |
| --- | --- |
| [`boctulus/simplerest-scaffolding`](https://github.com/boctulus/simplerest-scaffolding) | `make` commands, code generators, and PHP development tools |
| [`boctulus/simplerest-bootstrap-html`](https://github.com/boctulus/simplerest-bootstrap-html) | Bootstrap 5 forms, AdminLTE components, and Bootstrap pagination |
| [`boctulus/simplerest-fpdf`](https://github.com/boctulus/simplerest-fpdf) | `PdfBase`, a SimpleRest adapter around FPDF |
| [`boctulus/simplerest-web-inspection`](https://github.com/boctulus/simplerest-web-inspection) | CMS and technology detection plus HTML selector helpers |
| [`boctulus/simplerest-api-clients`](https://github.com/boctulus/simplerest-api-clients) | Google Drive, WooCommerce, OneSignal, and Hugging Face clients |
| [`boctulus/llm-providers`](https://github.com/boctulus/llm-providers) | Unified OpenAI, Anthropic, xAI, and Ollama provider interface |

The optional packages are distributed through GitHub Composer repositories;
Packagist registration is pending. The BasicACL and FineGrainedACL extensions
are proprietary and are not part of the public Composer catalogue.

## Requirements

The current `composer.json` declares PHP `>=8.1,<8.5`. It also declares Composer dependencies. Database drivers, database setup, web-server configuration, and optional integrations depend on which features an application uses; consult the relevant guide before deployment.

## Upload validation

`FileUploader` accepts only HTTP-uploaded files whose filename extension and detected MIME type are allowlisted. Applications that accept additional types must configure `files.media_accepted.extensions` and `files.media_accepted.mime_types`, or call `setAllowedTypes()` before upload. CLI code that supplies a local file path is rejected.

## Getting started

This checkout is an application repository as well as framework source. Composer metadata describes the package as `boctulus/simplerest` of type `library`; that fact alone does not establish that a clean consumer project can install and run the framework as a standalone application. The installation and clean-start guide is being reconstructed and must be verified from a clean environment before it is treated as a copy-and-run tutorial.

Start with the [canonical documentation map](docs/index.md). The [audit register](docs/audit/README.md) is internal; topic pages not linked from the map are legacy or pending audit input, not current specifications.

The curated Spanish VitePress site lives in [`docs-site/`](docs-site/); it is a derivative, not the canonical source. See [documentation publishing](.github/DOCS-PUBLISHING.md) for the GitHub Pages rollout, build checks, and publication criteria. The public URL must be confirmed after the deployment succeeds.

## Development checks

The Composer manifest currently defines these scripts:

```sh
composer test
composer cs
```

`composer test` invokes `phpunit --colors=always unit-tests`. The current `phpunit.xml` also lists `unit-tests/` as its configured suite. This verifies the test selection only; whether the command runs successfully has not been verified. `composer cs` invokes PHPStan against `src` at level 7.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

SimpleRest is distributed under the MIT License. See [LICENSE](LICENSE).
