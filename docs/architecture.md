# Architecture overview

This page records only the request and bootstrap relationships confirmed in the current checkout. It is an overview, not a complete inventory of framework components.

## Application entry points

- `index.php` requires `app.php`, then checks `web_router`, `console_router`, and `front_controller` in the application configuration and invokes each enabled component.
- `app.php` loads the boot and redirection scripts, Composer autoloading, application autoload rules, environment values, configuration, helpers, and configured providers.
- `com` is the repository's CLI entry point. Its complete command behavior is being documented separately after command discovery and examples are checked.

## HTTP routing and front controller

`config/config.php` currently enables the web router and front controller and provides the `front_behaviors` map. `index.php` includes `config/routes.php`, compiles the web routes, and resolves the current request. It then invokes `FrontController::resolve()` when enabled.

The front controller constructs handlers from `front_behaviors`. The current configuration names request, API, authentication, output, middleware, and error handlers. `FrontController::resolve()` selects HTTP or CLI context, parses request parameters, resolves the controller and action through the configured handlers, validates the target, and invokes it. Exact precedence and response behavior belong in the routing and request reference pages.

## Framework source and application code

Composer maps `Boctulus\Simplerest\Core\` to `src/framework/` and `Boctulus\Simplerest\` to `src/`. Development autoloading also maps the application namespace to `app/`. `app.php` registers an additional loader for module classes under `app/Modules/`.

This repository is both framework source and a configured application. The presence of a core directory does not, by itself, prove that the repository can be consumed as a standalone Composer library or that its application bootstrap is portable without the surrounding files.

## Evidence used

- `index.php`
- `app.php`
- `config/config.php`
- `config/routes.php`
- `src/framework/FrontController.php`
- `src/framework/WebRouter.php`
- `composer.json`

For current claims under review, see the [documentation audit register](audit/README.md).
