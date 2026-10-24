# Core

## Verified documentation

- [Architecture overview](../architecture.md) — verified entry-point, bootstrap, routing, and front-controller relationships for this checkout.
- [Request lifecycle](request-lifecycle.md) — source-traced router short-circuit, front-controller branches, and response finalization.
- [Routing HTTP](web-routing.md) — WebRouter registration, parameters, precedence, and source-level limits; no route was run.
- [Request y Response](request-response.md) — request input, response envelopes, clone helpers, and emission boundaries; source-traced, not runtime-tested.
- [ApiClient](api-client.md) — source-traced outbound HTTP client, response accessors, cache/auth helpers, and implementation limits; no network request was run.
- [Middleware](middleware.md) — registration and post-action behavior in WebRouter and FrontController; closure-route differences remain explicit.
- [FrontController handlers](handlers.md) — configured replacement keys and methods consumed by each handler.
- [Configuration and environment](configuration-environment.md) — Config, Env, global helpers, parsing behavior, and bootstrap loading limits.
- [Service providers](service-providers.md) — provider registration order and package config/route loading.
- [Dependency container](dependency-container.md) — explicit bindings, construction, and contract-lookup limits.
- [CORS](cors.md) — explicit handler invocation, origin/path matching, headers, and preflight behavior.
- [Sessions and cookies](sessions-cookies.md) — PHP session and cookie wrappers with their exposed parameters.
- [Error handling](error-handling.md) — FrontController and global exception paths, status mapping, and exception families.
- [Logging](logging.md) — Logger helpers, configuration gates, and filename limitations.

## Remaining runtime audit

These core references are source-traced; their runtime paths were not exercised. Legacy pages stay in the [pending audit area](../audit/pending/README.md) until their remaining claims are traced. The broader framework coverage plan is in the [documentation audit continuation](../to-do/in-progress/documentation-audit-continuation.md).
