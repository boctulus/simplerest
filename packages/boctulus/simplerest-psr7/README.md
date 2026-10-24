# SimpleRest PSR-7 Adapters

`boctulus/simplerest-psr7` wraps SimpleRest's `Request` and `Response` in
PSR-7 (`psr/http-message`) interfaces. It was extracted from the core so the
framework itself requires nothing but PHP.

The existing class names are kept:

- `Boctulus\Simplerest\Core\Psr7\ServerRequestAdapter`
- `Boctulus\Simplerest\Core\Psr7\ResponseAdapter`
- `Boctulus\Simplerest\Core\Psr7\StreamAdapter`
- `Boctulus\Simplerest\Core\Psr7\UriAdapter`

The helper functions `psr7_request()`, `psr7_response()`, `psr7_stream()`,
`psr7_uri()`, `psr7_json()`, `psr7_redirect()`, `psr7_html()` and
`psr7_text()` are autoloaded from `src/helpers.php`.
