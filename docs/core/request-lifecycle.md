# Request lifecycle

This page describes the source-traced entry-point and dispatch flow for this checkout. It is not a complete route-definition, request-helper, or response-format guide; those legacy claims remain under audit.

## Entry point and router short-circuit

`index.php` requires `app.php`, then checks the `web_router`, `console_router`, and `front_controller` switches in that order. All three are enabled in the checked-in `config/config.php`.

For HTTP requests, `index.php` includes `config/routes.php`, calls `WebRouter::compile()`, and then `WebRouter::resolve()`. A route that dispatches its callback or controller exits before `CliRouter` or `FrontController` can run. A package-specific `web_router` disable returns from the exact-alias branch or continues the normal route search; a WebRouter miss returns. `CliRouter::resolve()` returns outside CLI, so the enabled `FrontController::resolve()` receives the request when WebRouter does not dispatch it.

For CLI requests, `WebRouter::resolve()` returns immediately. `CliRouter::resolve()` gets the next opportunity to handle the arguments; the detailed command matching and fallback behavior are documented with the CLI audit.

`WebRouter::compile()` orders routes by wildcard presence, literal segments, total segments, then parameter count, with further tie-breaking in source. The current test source asserts that a static route precedes a parameter route; it does not establish every possible ordering tie.

## Front-controller path

`FrontController::resolve()` creates handler objects from the configured `front_behaviors` map, then calls the request handler to parse the HTTP path or CLI arguments. It returns when parsing produces no parameters. For non-empty parameters it resolves one branch: authentication, API, or an ordinary controller. It then applies the package front-controller switch when relevant, stores route arguments in `Request`, checks the resolved class and method, and checks the callable list for non-auth API methods before dispatch.

If the package front-controller switch disables the resolved package controller, the front controller returns before dispatch. Otherwise, after a controller call, output formatting runs only when the method returns non-null data. The middleware handler runs after the controller call. A non-empty response is flushed; then the front controller exits. A caught `Throwable` is sent to the configured error handler. These are conditional stages, not a six-method pipeline that every request traverses.

`RequestHandler::parse()` uses `REQUEST_URI` for HTTP, removes an `index.php` path segment, applies configured `base_url`, and splits the path. For CLI it reads `$argv`. It returns the path/argument array with authentication and API flags derived from `remove_api_slug` and the segments. This does not establish the behavior of every `Request` accessor.

## Response boundary and observed alias edge

`Response::flush()` selects an output path from response state and the request's `Accept` header, emits the stored body, and exits. WebRouter and FrontController reach it only on their respective branches.

There is a separate unverified edge in the exact URL-alias branch of `WebRouter::resolve()`: the branch sets response data and exits while the adjacent `Response::flush()` call is commented out. The source path is recorded in the [audit register](../audit/README.md#claim-level-findings-routing-and-request-lifecycle); no test or runtime reproduction establishes the user-visible result. Do not rely on this branch as a verified response path.

## Evidence and limits

- `unit-tests/web-router/WebRouterTest.php` asserts route registration, compiled patterns, and a static-before-parameter ordering case.
- `unit-tests/web-router/WebRouterFunctionalTest.php` contains HTTP assertions for grouped routes, parameters, route priority, and a non-200 result for a missing route. It targets a configured running server.
- `unit-tests/http/RequestImmutableMethodsTest.php` and `ResponseImmutableMethodsTest.php` assert clone behavior for selected fields; they do not assert request dispatch or response termination.

The tests above were inspected, not executed. `phpunit.xml` does not list `unit-tests/`, and no test establishes the `index.php` resolver order, process exits, conditional handler sequence, or alias response behavior. See the [PHPUnit discovery finding](../audit/README.md#claim-level-finding-phpunit-discovery) for the configured suite paths.

The former [Routing](../audit/pending/framework/Routing.md), [WebRouter](../audit/pending/framework/WebRouter.md), [FrontController](../audit/pending/framework/FrontController.md), [Request](../audit/pending/framework/Request.md), and [Response](../audit/pending/framework/Response.md) pages remain quarantined as audit inputs. Their unreviewed claims are not carried forward here.
