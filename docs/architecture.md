# Architecture overview

This page records only the request and bootstrap relationships confirmed in the current checkout. It is an overview, not a complete inventory of framework components.

## Application entry points

- `index.php` requires `app.php`, then checks `web_router`, `console_router`, and `front_controller` in that order. An enabled flag does not guarantee that every later component runs: a resolver can end the request first.
- `app.php` loads the boot and redirection scripts, Composer autoloading, application autoload rules, environment values, configuration, helpers, and configured providers.
- `com` is the repository's CLI entry point. Its complete command behavior is being documented separately after command discovery and examples are checked.

## HTTP routing and front controller

`config/config.php` currently enables the web router, CLI router, and front controller and provides the `front_behaviors` map. For an HTTP request, `index.php` includes `config/routes.php`, compiles the web routes, and calls `WebRouter::resolve()` first. When that resolver dispatches a matching route, it exits; `CliRouter` and `FrontController` are not reached. When no web route matches, the web resolver returns. `CliRouter::resolve()` then returns immediately outside CLI, after which `FrontController::resolve()` runs if enabled.

When the front controller is reached, it constructs handler objects from `front_behaviors`, parses the request, and chooses one resolution branch: authentication, API, or an ordinary controller. It validates the resolved class and method, checks the callable-method list for non-auth API requests, and invokes the target. Output formatting runs for non-null results, middleware runs after the controller call, and the error handler runs for caught `Throwable` values. These stages are conditional; not every configured handler method runs for every request. See [Request lifecycle](core/request-lifecycle.md) for the source-traced flow and its limits.

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
- `src/framework/CliRouter.php`
- `composer.json`

For current claims under review, see the [documentation audit register](audit/README.md).
