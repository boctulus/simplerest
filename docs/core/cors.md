# Cross-origin requests (CORS)

SimpleRest provides `CorsHandler` and a global `cors()` helper. Neither is installed automatically by bootstrap or `FrontController`; application code must call the helper or handler on the paths that need CORS behavior.

## Configuration and matching

`cors()` loads `config/cors.php`, creates a `CorsHandler`, applies the configuration, and calls `handle()`. The checked-in config has path `api/*`, wildcard origins, methods, and headers, no exposed headers, zero max age, and credentials disabled.

The handler parses the path from `REQUEST_URI` and compares it to each configured path with `fnmatch()`. The default pattern has no leading slash while a normal parsed request path does. The focused tests use `*`, so they do not establish that the checked-in `api/*` preset matches `/api/...`; confirm the path pattern in the target application before relying on it.

The handler returns without changing headers when the path is outside its configured patterns or when `HTTP_ORIGIN` is absent. `Referer` is not used as an Origin substitute. Origins can be allowed by exact entry, `*`, or a regex in `allowedOriginsPatterns`. A wildcard origin cannot be combined with credentials; that configuration throws `InvalidArgumentException`.

## Headers and preflight

For an allowed origin, the handler emits `Access-Control-Allow-Origin`, `Access-Control-Allow-Credentials`, and the configured allow/expose headers and methods when present. A wildcard method list expands to GET, POST, PUT, PATCH, DELETE, and OPTIONS. The handler writes these allow headers; it does not compare `Access-Control-Request-Method` or `Access-Control-Request-Headers` against the request before responding.

An allowed-origin OPTIONS request sets status 204 and `Content-Length: 0`, then exits. A disallowed Origin sets status 403 and throws `RuntimeException` with code 403. The caller's error path determines how that exception is rendered.

## Evidence and limits

`CorsHandlerOriginTest.php` asserts that a Referer does not replace a missing Origin and that a disallowed Origin throws code 403. The test source was inspected, not run; no CORS request or preflight was executed. The default path-pattern mismatch is recorded in the [core runtime audit](../audit/README.md#core-runtime-and-http-audit-2026-10-24).
