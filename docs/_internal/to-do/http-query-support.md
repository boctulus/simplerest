# HTTP QUERY support plan

## Objective

Add incremental support for RFC 10008 without changing the behavior of existing GET, POST, PUT, PATCH, DELETE, or OPTIONS endpoints.

The first release supports JSON QUERY requests on explicit WebRouter routes. Automatic ApiController resources are intentionally deferred until their read pipeline can consume URI and body criteria without duplicating the existing `get()` implementation.

## Stage 1 — WebRouter and transport support

Status: implemented on the task branch.

- [x] Add `WebRouter::query()`.
- [x] Centralize route registration and fix the missing current URI assignment in `WebRouter::put()`.
- [x] Include QUERY in `match()`, `any()`, and `fromArray()`.
- [x] Normalize custom methods to uppercase.
- [x] Normalize request header names case-insensitively.
- [x] Accept media type parameters such as `application/json; charset=utf-8`.
- [x] Parse JSON bodies for every HTTP method, including QUERY.
- [x] Validate QUERY Content-Type, body presence, and JSON container type.
- [x] Add QUERY and HEAD to the CORS wildcard expansion.
- [x] Handle CORS before WebRouter dispatch so QUERY preflights do not require an explicit OPTIONS route.
- [x] Normalize leading slashes when matching configured CORS paths.
- [x] Expose `Accept-Query` through the default CORS configuration.
- [x] Add QUERY support to ApiClient.
- [x] Fix the individual ACL callable bugs for POST and PATCH.
- [x] Add focused PHPUnit coverage and a Node.js live smoke test.
- [x] Run the focused PHPUnit suite in CI on PHP 8.1, 8.2, and 8.3.

### Stage 1 contract

```php
WebRouter::query('products/search', function () {
    return Product::search(request()->getBody(false));
});
```

```http
QUERY /products/search HTTP/1.1
Content-Type: application/json
Accept: application/json

{"status":"active"}
```

Stage 1 accepts `application/json` only. Missing Content-Type or body returns 400, unsupported media types return 415, malformed JSON returns 400, and scalar JSON query content returns 422.

`any()` routes now include QUERY. Applications upgrading SimpleRest must verify that callbacks registered with `any()` remain safe and idempotent when invoked through QUERY.

## Stage 2 — Automatic ApiController endpoints

- [ ] Add `query()` to the IApi contract and ApiController.
- [ ] Map QUERY to the same `read_all`, `list`, `list_all`, `show`, and `show_all` permissions as GET.
- [ ] Extract the current `ApiController::get()` read pipeline into one reusable internal operation.
- [ ] Keep URI parameters and query content separate in Request.
- [ ] Create adapters for the existing URI query DSL and its JSON body representation.
- [ ] Enable QUERY first for collection endpoints such as `/api/v1/products`.
- [ ] Compare GET and QUERY results for the same filters, ordering, projection, pagination, and tenant scope.
- [ ] Add regression tests for role permissions and individual ACL bitmasks.

The first automatic JSON representation should preserve the existing SimpleRest query DSL:

```json
{
  "fields": "id,name,cost",
  "cost": {"between": "49,100"},
  "order": {"cost": "ASC"},
  "page": 1,
  "pageSize": 50
}
```

## Stage 3 — RFC discovery and advanced HTTP behavior

- [ ] Add route-specific accepted query media types.
- [ ] Emit `Accept-Query` on OPTIONS and applicable resource responses.
- [ ] Emit accurate `Allow` headers on 405 responses.
- [ ] Add `Location` and `Content-Location` support for equivalent resources.
- [ ] Add ETag and conditional QUERY coverage.
- [ ] Evaluate cache keys that include query content and relevant metadata.
- [ ] Test redirects, retries, reverse proxies, WAFs, Cloudflare, Apache, Nginx, and PHP-FPM.
- [ ] Document OpenAPI representation while native QUERY support remains ecosystem-dependent.

## Verification

Focused unit suite:

```bash
composer test:http-query
```

Live transport smoke test against any explicit QUERY route:

```bash
npm run test:http-query:smoke -- http://simplerest.lan/products/search
```

Equivalent curl check:

```bash
curl -i -X QUERY \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  --data '{"status":"active"}' \
  http://simplerest.lan/products/search
```

For cross-origin browser tests, verify that the OPTIONS response includes QUERY in `Access-Control-Allow-Methods` before checking the actual QUERY request in DevTools.
