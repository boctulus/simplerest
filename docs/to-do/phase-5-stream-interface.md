# Fase 5: StreamInterface Body (Advanced PSR-7)

**Autor**: Pablo Bozzolo (boctulus)
**Estado**: 📋 Planeado (No iniciado)
**Prioridad**: Baja-Media
**Dependencias**:
- Fase 1 (PSR-7 Adapters) ✅ Completada
- Fase 3 (PSR-17 Factories) ⏳ Planeada
- Fase 4 (PSR-15 Middleware) ⏳ Planeada

---

## Objetivos

Reemplazar el manejo de **body como string/array** por **`StreamInterface`** en las clases `Request` y `Response`, proporcionando soporte completo para:

1. **Grandes archivos** (uploads, downloads) sin consumir toda la memoria
2. **Streaming responses** (server-sent events, chunked encoding)
3. **Lazy loading** de bodies grandes
4. **Compatibilidad PSR-7 nativa** (100% sin adapters)

---

## Contexto

### Estado Actual

#### Request Body

**Actual** (`app/Core/Request.php`):
```php
class Request
{
    private $body;  // string|array

    public function getBody($as_object = false)
    {
        if ($this->body !== null) {
            return $this->body;
        }

        $body = file_get_contents('php://input');
        // ... parse JSON/XML/FormData
        return $this->body;
    }
}
```

**Problemas:**
- ❌ Lee TODO el body en memoria con `file_get_contents()`
- ❌ No soporta streaming
- ❌ Archivo de 1GB consume 1GB de RAM
- ❌ No es PSR-7 compliant (PSR-7 requiere `StreamInterface`)

#### Response Body

**Actual** (`app/Core/Response.php`):
```php
class Response
{
    private $data;  // mixed (array, string, object)

    public function send($data = null, int $code = 200)
    {
        if (is_array($data)) {
            echo json_encode($data);
        } else {
            echo $data;
        }
    }
}
```

**Problemas:**
- ❌ Todo el response se carga en memoria antes de `echo`
- ❌ No soporta chunked encoding
- ❌ No soporta streaming (SSE, large files)

---

## Alcance de Implementación

### Fase 5.1: StreamInterface Body en Request

#### Objetivo

Reemplazar `$body` (string/array) por `StreamInterface`:

**Después** (Fase 5):
```php
class Request
{
    private ?StreamInterface $body = null;  // PSR-7 Stream

    public function getBody(): StreamInterface
    {
        if ($this->body === null) {
            // Crear stream lazy desde php://input
            $this->body = new StreamAdapter(fopen('php://input', 'r'));
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
}
```

**Beneficios:**
- ✅ Lazy loading: solo lee cuando se accede
- ✅ Memory efficient: no carga todo en RAM
- ✅ Streamable: soporta `read()`, `seek()`, `rewind()`
- ✅ PSR-7 nativo: no necesita adapters

#### Compatibilidad con Código Existente

**Problema**: Código existente espera `array`:
```php
$data = $request->getBody(true);  // Espera array, no Stream
```

**Solución**: Agregar método `getParsedBody()`:
```php
public function getParsedBody(): ?array
{
    if ($this->parsedBody !== null) {
        return $this->parsedBody;
    }

    $contentType = $this->getHeaderLine('Content-Type');

    if (str_contains($contentType, 'application/json')) {
        $bodyString = (string) $this->getBody();
        $this->parsedBody = json_decode($bodyString, true);
    }

    return $this->parsedBody;
}
```

**Migración gradual**:
```php
// ❌ Código viejo (sigue funcionando con deprecation)
$data = $request->getBody(true);

// ✅ Código nuevo (PSR-7 compliant)
$data = $request->getParsedBody();
```

### Fase 5.2: StreamInterface Body en Response

#### Objetivo

Reemplazar `$data` por `StreamInterface`:

**Después** (Fase 5):
```php
class Response
{
    private ?StreamInterface $body = null;

    public function getBody(): StreamInterface
    {
        if ($this->body === null) {
            $this->body = new StreamAdapter('');
        }

        return $this->body;
    }

    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    public function send()
    {
        // Enviar headers
        http_response_code($this->http_code);
        foreach ($this->headers as $header) {
            header($header);
        }

        // Stream body (chunked si es grande)
        $body = $this->getBody();
        $body->rewind();

        while (!$body->eof()) {
            echo $body->read(8192);  // 8KB chunks
            flush();
        }
    }
}
```

### Fase 5.3: Soporte para Grandes Archivos

#### Upload de Archivos Grandes

**Antes** (consume toda la RAM):
```php
$fileContent = file_get_contents($_FILES['file']['tmp_name']);  // 1GB file = 1GB RAM
```

**Después** (streaming):
```php
$stream = new StreamAdapter(fopen($_FILES['file']['tmp_name'], 'r'));

// Procesar en chunks
while (!$stream->eof()) {
    $chunk = $stream->read(8192);
    $this->processChunk($chunk);
}
```

#### Download de Archivos Grandes

**Antes** (consume toda la RAM):
```php
$fileContent = file_get_contents('/path/to/large.zip');  // 500MB en RAM
$response->send($fileContent);
```

**Después** (streaming):
```php
$stream = new StreamAdapter(fopen('/path/to/large.zip', 'r'));

$response = Response::getInstance()
    ->withBody($stream)
    ->withHeader('Content-Type', 'application/zip')
    ->withHeader('Content-Disposition', 'attachment; filename="large.zip"')
    ->send();  // Stream en chunks de 8KB
```

### Fase 5.4: Server-Sent Events (SSE)

```php
class EventStreamResponse extends Response
{
    public function sendEvent(string $event, $data): void
    {
        $message = "event: {$event}\n";
        $message .= "data: " . json_encode($data) . "\n\n";

        echo $message;
        flush();
    }

    public function stream()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        while (true) {
            $data = $this->getRealtimeData();
            $this->sendEvent('update', $data);

            sleep(1);
        }
    }
}
```

### Fase 5.5: Chunked Transfer Encoding

```php
class ChunkedResponse extends Response
{
    public function sendChunked(StreamInterface $body)
    {
        header('Transfer-Encoding: chunked');

        $body->rewind();
        while (!$body->eof()) {
            $chunk = $body->read(8192);
            $chunkSize = dechex(strlen($chunk));

            echo "{$chunkSize}\r\n{$chunk}\r\n";
            flush();
        }

        echo "0\r\n\r\n";  // Terminating chunk
    }
}
```

---

## Tareas de Implementación

### Fase 5.1: Refactorizar Request

- [ ] Cambiar `private $body` a `private ?StreamInterface $body`
- [ ] Refactorizar `getBody()` para retornar `StreamInterface`
- [ ] Implementar `getParsedBody(): ?array` (PSR-7 standard)
- [ ] Implementar `withParsedBody($data): self`
- [ ] Lazy loading desde `php://input`
- [ ] Deprecar `getBody($as_object)` con parámetro

### Fase 5.2: Refactorizar Response

- [ ] Cambiar `private $data` a `private ?StreamInterface $body`
- [ ] Refactorizar `send()` para streamear body
- [ ] Implementar chunked streaming (8KB chunks)
- [ ] Mantener helpers `json()`, `html()` usando streams internamente

### Fase 5.3: Helpers para Streams

- [ ] Crear `app/Core/Helpers/streams.php`
- [ ] Función `stream_from_file(string $path): StreamInterface`
- [ ] Función `stream_from_string(string $content): StreamInterface`
- [ ] Función `stream_to_file(StreamInterface $stream, string $path): void`
- [ ] Función `stream_to_string(StreamInterface $stream): string`

### Fase 5.4: Clases de Streaming Avanzado

- [ ] Crear `app/Core/Responses/StreamResponse.php` (base para streaming)
- [ ] Crear `app/Core/Responses/FileResponse.php` (download de archivos)
- [ ] Crear `app/Core/Responses/EventStreamResponse.php` (SSE)
- [ ] Crear `app/Core/Responses/ChunkedResponse.php` (chunked encoding)

### Fase 5.5: Testing

- [ ] Crear `tests/unit-tests/StreamRequestTest.php`
- [ ] Test `getBody()` retorna `StreamInterface`
- [ ] Test `getParsedBody()` parsea JSON
- [ ] Test lazy loading no lee body hasta acceso
- [ ] Crear `tests/unit-tests/StreamResponseTest.php`
- [ ] Test `send()` streamea en chunks
- [ ] Test `FileResponse` no carga archivo completo en RAM
- [ ] Test `EventStreamResponse` envía SSE
- [ ] **Meta**: 25+ tests, 70+ assertions, 100% passing

### Fase 5.6: Benchmarking

- [ ] Crear `tests/benchmarks/StreamingBenchmark.php`
- [ ] Benchmark: Upload 100MB file (antes vs después)
- [ ] Benchmark: Download 500MB file (antes vs después)
- [ ] Benchmark: Memory usage (antes vs después)
- [ ] **Meta**: Reducción de 90%+ en uso de memoria

### Fase 5.7: Documentación

- [ ] Crear `docs/Streaming.md` con:
  - Introducción a Streaming
  - Lazy loading de bodies
  - Uploads de archivos grandes
  - Downloads de archivos grandes
  - Server-Sent Events (SSE)
  - Chunked transfer encoding
  - Best practices
  - Migration guide
- [ ] Actualizar `docs/PSR-7.md` con cambios de Fase 5
- [ ] Actualizar `docs/CHANGELOG-PSR.md` con Fase 5

---

## Estimación de Esfuerzo

| Tarea | Tiempo Estimado | Complejidad |
|-------|-----------------|-------------|
| Refactorizar Request con Stream | 4-5 horas | Alta |
| Refactorizar Response con Stream | 4-5 horas | Alta |
| Helpers para streams | 2 horas | Media |
| Clases de streaming avanzado | 3-4 horas | Alta |
| Testing completo | 5-6 horas | Alta |
| Benchmarking | 2-3 horas | Media |
| Documentación | 3 horas | Media |
| **TOTAL** | **23-30 horas** | **Muy Alta** |

---

## Dependencias Externas

### Ninguna Nueva

Todas las dependencias ya fueron instaladas en fases anteriores:
- `psr/http-message:^2.0` (Fase 1) - incluye `StreamInterface`

---

## Riesgos y Mitigaciones

### Riesgo 1: Breaking Changes Masivos

**Descripción**: Cambiar `getBody()` de retornar `array` a `StreamInterface` rompe TODO el código existente.

**Mitigación**:
- **Deprecar** `getBody($as_object)` pero mantenerlo funcionando
- **Introducir** `getParsedBody()` como nuevo método PSR-7
- **Guía de migración** paso a paso
- **Adapter temporal** que convierte Stream a array si se usa parámetro viejo

**Código de compatibilidad:**
```php
public function getBody($as_object = null): StreamInterface|array
{
    if ($as_object !== null) {
        // Modo legacy (deprecated)
        trigger_error('getBody($as_object) is deprecated, use getParsedBody()', E_USER_DEPRECATED);
        return $this->getParsedBody();
    }

    // Modo PSR-7 (nuevo)
    return $this->bodyStream;
}
```

### Riesgo 2: Performance Regression

**Descripción**: Streaming puede ser más lento que `file_get_contents()` para archivos pequeños.

**Mitigación**:
- **Threshold**: Si body < 1MB, cargar todo en memoria (fast path)
- **Benchmark** exhaustivo antes de merge
- **Lazy loading**: Solo streamear cuando sea necesario

### Riesgo 3: Memory Leaks

**Descripción**: Streams mal cerrados pueden causar file descriptor leaks.

**Mitigación**:
- **`__destruct()`**: Cerrar streams automáticamente
- **Testing**: Tests de memory leaks
- **Documentación**: Best practices para cerrar streams

### Riesgo 4: Complejidad para Usuarios

**Descripción**: Trabajar con Streams es más complejo que arrays.

**Mitigación**:
- **Helpers**: Funciones simples como `stream_to_array()`
- **Ejemplos**: Casos de uso comunes documentados
- **Gradual adoption**: No forzar uso de Streams si no es necesario

---

## Casos de Uso

### 1. Upload de Archivo Grande (sin consumir RAM)

**Antes** (Fase 1-4):
```php
// ❌ Carga 1GB en RAM
$content = file_get_contents($_FILES['file']['tmp_name']);
Storage::save('uploads/file.zip', $content);
```

**Después** (Fase 5):
```php
// ✅ Stream directo, 0MB en RAM
$stream = stream_from_file($_FILES['file']['tmp_name']);
stream_to_file($stream, 'uploads/file.zip');
```

### 2. Download de Archivo Grande

**Antes**:
```php
// ❌ Carga 500MB en RAM
$content = file_get_contents('/path/to/large.pdf');
response()->send($content);
```

**Después**:
```php
// ✅ Stream en chunks de 8KB
$response = new FileResponse('/path/to/large.pdf');
$response->send();
```

### 3. Server-Sent Events (Realtime Updates)

```php
Route::get('/events', function() {
    $sse = new EventStreamResponse();

    return $sse->stream(function($event) {
        while (true) {
            $data = DB::table('notifications')->latest()->first();
            $event->send('notification', $data);

            sleep(2);
        }
    });
});
```

**Client-side:**
```javascript
const eventSource = new EventSource('/events');

eventSource.addEventListener('notification', (e) => {
    const data = JSON.parse(e.data);
    console.log('New notification:', data);
});
```

### 4. Procesamiento de CSV Grande

**Antes**:
```php
// ❌ Carga 200MB CSV en RAM
$csv = file_get_contents('data.csv');
$lines = explode("\n", $csv);
foreach ($lines as $line) {
    $this->processRow($line);
}
```

**Después**:
```php
// ✅ Lee línea por línea (streaming)
$stream = stream_from_file('data.csv');

while (!$stream->eof()) {
    $line = $this->readLine($stream);  // Helper que lee hasta \n
    $this->processRow($line);
}
```

### 5. Proxy HTTP (Stream Through)

```php
Route::get('/proxy', function() {
    // Fetch remote file sin cargarlo en RAM
    $remoteStream = fopen('https://example.com/large-video.mp4', 'r');
    $stream = new StreamAdapter($remoteStream);

    return Response::getInstance()
        ->withBody($stream)
        ->withHeader('Content-Type', 'video/mp4')
        ->send();  // Stream directo al cliente
});
```

---

## Breaking Changes

### ⚠️ Potential Breaking Changes

| Cambio | Impacto | Mitigación |
|--------|---------|------------|
| `getBody()` retorna `StreamInterface` | **ALTO** | Deprecar pero mantener compatible con parámetro |
| `getParsedBody()` reemplaza `getBody(true)` | Medio | Guía de migración |
| `send()` streamea en lugar de `echo` directo | Bajo | Transparente para usuarios |

### Estrategia de Migración

**Opción 1: Gradual (Recomendada)**
- Fase 5a: Introducir `getParsedBody()`, deprecar `getBody($as_object)`
- Fase 5b: Cambiar interno a Stream pero mantener API compatible
- Fase 5c: Remover deprecations en major version (v2.0)

**Opción 2: Breaking (Solo si major version)**
- Cambiar directamente en v2.0
- No mantener compatibilidad
- Requiere actualización de todo el código

---

## Criterios de Aceptación

- ✅ `Request::getBody()` retorna `StreamInterface`
- ✅ `Response::getBody()` retorna `StreamInterface`
- ✅ `getParsedBody()` parsea JSON/XML/FormData
- ✅ Upload de archivo 1GB usa < 50MB RAM
- ✅ Download de archivo 500MB usa < 50MB RAM
- ✅ Server-Sent Events funcionan
- ✅ Chunked transfer encoding funciona
- ✅ 100% de tests pasan (25+ tests, 70+ assertions)
- ✅ Benchmarks muestran reducción 90%+ en RAM
- ✅ Documentación completa en `docs/Streaming.md`
- ✅ Backward compatibility con código existente (via deprecations)

---

## Benchmarks Esperados

### Upload 100MB File

| Métrica | Antes (Fase 1-4) | Después (Fase 5) | Mejora |
|---------|------------------|------------------|--------|
| **Memory Peak** | 105 MB | 8 MB | **92% ↓** |
| **Time** | 1.2s | 1.3s | -8% (acceptable) |

### Download 500MB File

| Métrica | Antes (Fase 1-4) | Después (Fase 5) | Mejora |
|---------|------------------|------------------|--------|
| **Memory Peak** | 520 MB | 12 MB | **97% ↓** |
| **Time** | 2.5s | 2.6s | -4% (acceptable) |

### Parse 10MB JSON

| Métrica | Antes (Fase 1-4) | Después (Fase 5) | Mejora |
|---------|------------------|------------------|--------|
| **Memory Peak** | 25 MB | 25 MB | 0% (same) |
| **Time** | 0.3s | 0.3s | 0% (same) |

**Conclusión**: Streaming reduce drásticamente uso de memoria en archivos grandes, sin penalización significativa de performance.

---

## Referencias

- [PSR-7: HTTP Message Interfaces](https://www.php-fig.org/psr/psr-7/)
- [PHP Streams Documentation](https://www.php.net/manual/en/book.stream.php)
- [Server-Sent Events (MDN)](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events)
- [Chunked Transfer Encoding (RFC 7230)](https://tools.ietf.org/html/rfc7230#section-4.1)

---

## Notas Adicionales

### ¿Es necesaria la Fase 5?

**Depende del caso de uso:**

- **NO necesaria** si:
  - Requests/responses son siempre < 10MB
  - No hay uploads/downloads de archivos grandes
  - No se usa streaming (SSE, chunked)

- **NECESARIA** si:
  - Se manejan archivos > 50MB
  - Se requiere streaming en tiempo real
  - Se quiere 100% PSR-7 compliance sin adapters
  - Se busca optimizar uso de memoria

**Recomendación**: Implementar solo si hay necesidad real de streaming.

### Alternativa: Fase 5 Parcial

En lugar de refactorizar todo, implementar solo clases de streaming opcionales:

- `StreamResponse` para descargas grandes
- `EventStreamResponse` para SSE
- Mantener `Request`/`Response` con body como string/array

Esto da beneficios de streaming sin breaking changes.

---

## Archivos a Crear/Modificar

```
app/Core/Request.php (modificar)
├── Cambiar $body a StreamInterface
├── Agregar getParsedBody()
└── Deprecar getBody($as_object)

app/Core/Response.php (modificar)
├── Cambiar $data a StreamInterface
├── Refactorizar send() para streaming
└── Mantener helpers json()/html()

app/Core/Helpers/streams.php (crear)
├── stream_from_file()
├── stream_from_string()
├── stream_to_file()
└── stream_to_string()

app/Core/Responses/ (crear directorio)
├── StreamResponse.php
├── FileResponse.php
├── EventStreamResponse.php
└── ChunkedResponse.php

tests/unit-tests/ (crear)
├── StreamRequestTest.php
├── StreamResponseTest.php
└── StreamingBenchmark.php

docs/
├── Streaming.md (crear)
├── PSR-7.md (actualizar)
└── CHANGELOG-PSR.md (actualizar)
```

---

**Última Actualización**: 2025-01-29
**Estado**: 📋 Planeado (Prioridad Baja-Media)
**Fase Anterior**: [Fase 4: PSR-15 Middleware](./phase-4-psr15-middleware.md)
