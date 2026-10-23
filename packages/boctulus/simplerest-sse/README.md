# SimpleRest SSE

`boctulus/simplerest-sse` contains the `SSE` helper for emitting
Server-Sent Events from a PHP response. The existing class is
`Boctulus\Simplerest\SW\core\libs\SSE`.

The consuming web server and application must allow long-lived streaming
responses and flush output for events to reach the client promptly.
