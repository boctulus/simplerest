# Response emits an HTTP/2 status line over an HTTP/1.x connection

**Status:** open

**Confidence:** confirmed by local raw-socket probes

**Severity:** high; tested curl client rejects the response before reading its body

## Finding

The PHP built-in server received HTTP/1.1 requests, but SimpleRest emitted textual status lines beginning `HTTP/2` for 400, 401, and 403 responses. HTTP/2 does not use a textual status line. The bundled Windows `curl.exe` rejected each response with `Unsupported HTTP version (2.0)`.

## Reproduction and observed responses

Start a local server with `php -S 127.0.0.1:18765 -t . index.php`, then issue the following requests using a raw TCP client so the nonstandard status line can be inspected:

| Request | Status line | JSON message |
| --- | --- | --- |
| GET `/api/v1/products`, `Authorization: Basic dGVzdA==` | `HTTP/2 400` | `{"status":400,"error":{"type":null,"code":null,"message":"Authorization jwt token not found","detail":null,"location":null}}` |
| GET `/api/v1/products`, `Authorization: Bearer invalid.token.value` | `HTTP/2 401` | `{"status":401,"error":{"type":null,"code":null,"message":"Malformed UTF-8 characters","detail":null,"location":null}}` for this token sample |
| GET a temporary API controller with an empty callable list | `HTTP/2 403` | `{"status":403,"error":{"type":null,"code":null,"message":"Not authorized for Boctulus\\Simplerest\\Controllers\\api\\AuditAclDenyProbe:get","detail":null,"location":null}}` |

For all three, `curl.exe -i` exited with code 1 and reported `Unsupported HTTP version (2.0)`. The 403 route used a temporary test controller, removed after the probe; it did not exercise the app database or a live FineGrainedACL rule. JSON bodies were captured again over raw TCP on 2026-09-23; the dynamic `Date` header is omitted. Status and body observations are specific to these local requests/configuration.

## Expected behavior

Emit a valid status through the SAPI (`http_response_code()`) or a protocol-correct status line for the negotiated HTTP version. Do not send a textual `HTTP/2` status line on HTTP/1.x.

## Source locations

- `src/framework/Response.php:17` — default textual protocol version is `2`.
- `src/framework/Response.php:212`, `286`, `336`, and `515` — response methods construct `HTTP/` status lines from that value.
