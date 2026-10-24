# Request lifecycle

This page describes the source-traced HTTP request path for this checkout. The process-wide choice of HTTP components belongs to [Architecture](../architecture.md); this guide follows only the HTTP path. It is not a complete route-definition, request-helper, or response-format guide; those legacy claims remain under audit.

## HTTP route handoff

`index.php` requires `app.php`. When `web_router` is enabled, it includes `config/routes.php`, compiles those routes, and tries to dispatch the HTTP request. A dispatched route ends this branch. On a miss, the request can continue to the `FrontController` when that component is enabled.

If a package-specific `web_router` switch disables a route, the exact-alias branch returns to the caller; the ordinary route-search branch continues looking for another match. A WebRouter miss also returns to the caller.

`WebRouter::compile()` orders routes by wildcard presence, literal segments, total segments, then parameter count, with further tie-breaking in source. The current test source asserts that a static route precedes a parameter route; it does not establish every possible ordering tie.

## HTTP front-controller path

When an HTTP request reaches `FrontController::resolve()`, it creates handler objects from the configured `front_behaviors` map and asks the request handler to parse the path. It returns when parsing produces no parameters. Otherwise, it resolves one branch: authentication, API, or an ordinary controller. It then applies the package front-controller switch when relevant, stores route arguments in `Request`, checks the resolved class and method, and checks the callable list for non-auth API methods before dispatch.

If the package front-controller switch disables the resolved package controller, the front controller returns before dispatch. Otherwise, after a controller call, output formatting runs only when the method returns non-null data. The middleware handler runs after the controller call. A non-empty response is flushed; then the front controller exits. A caught `Throwable` is sent to the configured error handler. These are conditional stages, not a six-method pipeline that every request traverses.

For HTTP, `RequestHandler::parse()` reads `REQUEST_URI`, removes an `index.php` path segment, applies configured `base_url`, and splits the path. It returns the path with authentication and API flags derived from `remove_api_slug` and the segments. This does not establish the behavior of every `Request` accessor.

## Response boundary and observed alias edge

`Response::flush()` selects an output path from response state and the request's `Accept` header, emits the stored body, and exits. WebRouter and FrontController reach it only on their respective branches.

There is a separate unverified edge in the exact URL-alias branch of `WebRouter::resolve()`: the branch sets response data and exits while the adjacent `Response::flush()` call is commented out. The source path is recorded in the [audit register](../audit/README.md#claim-level-findings-routing-and-request-lifecycle); no test or runtime reproduction establishes the user-visible result. Do not rely on this branch as a verified response path.

## Evidence and limits

- `unit-tests/web-router/WebRouterTest.php` asserts route registration, compiled patterns, and a static-before-parameter ordering case.
- `unit-tests/web-router/WebRouterFunctionalTest.php` contains HTTP assertions for grouped routes, parameters, route priority, and a non-200 result for a missing route. It targets a configured running server.
- `unit-tests/http/RequestImmutableMethodsTest.php` and `ResponseImmutableMethodsTest.php` assert clone behavior for selected fields; they do not assert request dispatch or response termination.

The test sources above were inspected, not executed in this audit. Both `composer test` and `phpunit.xml` include `unit-tests/`; this establishes test selection only, not a passing run. In the historical Actions run `39725153614` for commit `0c7ef58`, test loading exited when `ApiCollectionsTest.php` required `app.php` and Dotenv could not read `.env`. No test establishes the HTTP route handoff, process exits, conditional handler sequence, or alias response behavior. See the [PHPUnit discovery finding](../audit/README.md#claim-level-finding-phpunit-discovery) for the configured suite and the limits of that CI run.

The former [Routing](../audit/pending/framework/Routing.md), [WebRouter](../audit/pending/framework/WebRouter.md), [FrontController](../audit/pending/framework/FrontController.md), [Request](../audit/pending/framework/Request.md), and [Response](../audit/pending/framework/Response.md) pages remain quarantined as audit inputs. Their unreviewed claims are not carried forward here.
