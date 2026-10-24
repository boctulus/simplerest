# Core

## Verified documentation

- [Architecture overview](../architecture.md) — verified entry-point, bootstrap, routing, and front-controller relationships for this checkout.
- [Request lifecycle](request-lifecycle.md) — source-traced router short-circuit, front-controller branches, and response finalization.
- [ApiClient](api-client.md) — source-traced outbound HTTP client, response accessors, cache/auth helpers, and implementation limits; no network request was run.

## Pending audit

Route-definition examples, the `Request` and `Response` helper APIs, middleware behavior beyond the dispatch order, and dependency wiring remain under review. Legacy pages stay in the [pending audit area](../audit/pending/README.md) until their claims are traced.
