---

title: "Adaptadores PSR-7 y SSE"
source:
  - docs/packages/http-adapters.md
source_revision: 379f2383edbe4978e934195d6a6ed4102651d154
status: partial
---

# Adaptadores PSR-7 y SSE

Estos paquetes extienden la frontera de respuesta HTTP con contratos distintos. PSR-7 adapta objetos del core a interfaces estándar; SSE emite un flujo de eventos sobre una respuesta de larga duración.

## Adaptadores PSR-7

`boctulus/simplerest-psr7` requiere `psr/http-message ^2.0` y conserva las clases:

- `ServerRequestAdapter` envuelve el `Request` del core.
- `ResponseAdapter` envuelve `Response` y expone `getSimpleRestResponse()` y `send()`.
- `StreamAdapter` implementa `StreamInterface`.
- `UriAdapter` implementa `UriInterface`.

El archivo `src/helpers.php` registra `psr7_request()`, `psr7_response()`, `psr7_stream()`, `psr7_uri()`, `psr7_json()`, `psr7_redirect()`, `psr7_html()` y `psr7_text()` mediante Composer `autoload.files`. Son helpers opcionales; instalar este paquete no sustituye el [ciclo HTTP del core](/core/request-lifecycle) ni demuestra que otro middleware PSR-7 quede instalado.

## Server-Sent Events

`boctulus/simplerest-sse` proporciona `Boctulus\Simplerest\SW\core\libs\SSE`. En contexto web, el constructor intenta enviar `Content-Type: text/event-stream` cuando todavía no se enviaron headers. `setDefaultChannel()`, `setInterval()` y `setRetry()` controlan el canal, pausa y dato de reconexión; `send()` serializa arrays como JSON, emite el evento, vacía buffers y duerme el intervalo. `sendError()` emite un campo `error` y termina el proceso con `exit`.

El servidor web, PHP-FPM y proxies deben tolerar conexiones largas y vaciado de buffers. El helper no instala broker, scheduler, reconexión de cliente ni autorización del endpoint. No se mantuvo una conexión SSE durante esta revisión.
