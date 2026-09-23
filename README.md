# SimpleRest

SimpleRest is a PHP framework for building HTTP applications and REST APIs. This repository contains the framework source, an application bootstrap, configuration and routing files, a command-line entry point, tests, examples, and locally developed packages.

The framework has its own runtime and conventions. Its behavior is defined by the code in this repository; similarities to other PHP frameworks are not a substitute for the SimpleRest API or architecture.

## Repository at a glance

- `src/framework/` — framework classes, including the HTTP and CLI routers, request pipeline, API base classes, database utilities, and other core components.
- `app/` — application controllers, models, commands, schemas, views, and modules used by this checkout.
- `config/` — application configuration and route registration.
- `database/` — database migration files used by this application.
- `unit-tests/` — PHPUnit test suite.
- `examples/` — example code.
- `packages/` — packages maintained in this repository.
- `docs/` — topic documentation and the documentation audit record.

`index.php` loads `app.php`, then invokes the enabled web router, CLI router, and front controller according to `config/config.php`. The front controller builds its request-processing handlers from the `front_behaviors` configuration. See [Architecture](docs/architecture.md) for the evidence-backed overview.

## Requirements

The current `composer.json` declares PHP `>=8.1,<8.5`. It also declares Composer dependencies. Database drivers, database setup, web-server configuration, and optional integrations depend on which features an application uses; consult the relevant guide before deployment.

## Getting started

This checkout is an application repository as well as framework source. Composer metadata describes the package as `boctulus/simplerest` of type `library`; that fact alone does not establish that a clean consumer project can install and run the framework as a standalone application. The installation and clean-start guide is being reconstructed and must be verified from a clean environment before it is treated as a copy-and-run tutorial.

Start with the [documentation map](docs/README.md) and [documentation status and evidence rules](docs/audit/README.md). The existing topic files are being audited against source and tests; until that audit is complete, use them as material to review, not as authoritative specifications.

## Development checks

The Composer manifest currently defines these scripts:

```sh
composer test
composer cs
```

`composer test` invokes PHPUnit against `unit-tests`; `composer cs` invokes PHPStan against `src` at level 7. Run checks in an environment with the repository dependencies installed.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

SimpleRest is distributed under the MIT License. See [LICENSE](LICENSE).
