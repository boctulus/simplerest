---
title: Proveedores LLM
source:
  - docs/integrations/llm-providers.md
source_revision: a052e20aa20c0ec1f4289cf7f6edf8856125b889
status: partial
---

# Proveedores LLM

`boctulus/simplerest-llm-providers` es un paquete opcional fuera del core. Su interfaz común cubre OpenAI, Anthropic Claude, xAI Grok y Ollama, con diferencias entre respuestas, parámetros y configuración. Esta guía describe el código de este checkout; no acredita credenciales funcionales, disponibilidad actual de modelos ni peticiones externas exitosas.

## Paquete y carga

El manifiesto declara PHP `>=8.1,<8.5` y `boctulus/simplerest ^2.0 || ^3.0`, con PSR-4 `Boctulus\LLMProviders\` hacia `src/`. Declara `Boctulus\LLMProviders\ServiceProvider` en `extra.simplerest.providers`, pero el descubrimiento automático en una aplicación consumidora no se verificó. El `ServiceProvider` incluye `config/cli_routes.php` en `boot()`; no carga `config/config.php` ni registra `LLMFactory` en el contenedor.

La configuración del paquete es una referencia y `LLMFactory::create()` no la carga. OpenAI, Claude y Grok leen `Config::get()` como alternativa a una clave explícita (`openai_api_key`, `claude_api_key`, `xai_api_key`). Ollama no usa clave. El comentario del archivo de configuración, las opciones de la factory y el ejemplo del README tienen diferencias; esta página refleja el código ejecutable.

## Crear una instancia

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

$llm = LLMFactory::create('openai', [
    'api_key' => getenv('OPENAI_API_KEY'),
]);

$llm->setModel('gpt-4o-mini')
    ->setParams(['max_tokens' => 400, 'temperature' => 0.4])
    ->addContent('Resume el propósito de esta aplicación.');

// exec() envía una petición externa al provider.
// $response = $llm->exec();
```

La factory recorta espacios y convierte el alias a minúsculas. Los nombres admitidos en esta revisión son:

| Provider | Alias | Modelo inicial en el constructor | Clave/ubicación leída |
| --- | --- | --- | --- |
| OpenAI | `openai`, `chatgpt`, `gpt` | `gpt-4o-mini` | `api_key` o `Config::get()['openai_api_key']` |
| Anthropic | `claude`, `anthropic` | `claude-sonnet-4-6` | `api_key` o `Config::get()['claude_api_key']`; API version predeterminada `2023-06-01` |
| xAI | `grok`, `xai` | `grok-4.7` | `api_key` o `Config::get()['xai_api_key']` |
| Ollama | `ollama` | Vacío; se debe establecer antes de `exec()` | Sin clave; URL local fija `http://localhost:11434` |

`create($provider, $config)` sólo consume `api_key` y, para Claude, `api_version`. No usa `default_provider`, `providers[*].default_model` ni `default_params` del archivo de referencia. La configuración que se pase a `create('ollama', ...)` se ignora.

## Contrato común

`LLMProviderInterface` declara:

- `setModel()` / `getModel()` y `setParams()` para elegir modelo y reemplazar los parámetros de la solicitud.
- `setMaxTokens()` / `getMaxTokens()` y `setTemperature()` como atajos de configuración.
- `addContent($content, $role = 'user')` para agregar mensajes a la instancia.
- `exec(?string $model = null)` para enviar una solicitud y devolver un array.
- `getContent()`, `getTokenUsage()`, `getFinishReason()`, `isComplete()`, `wereTokenEnough()` y `error()` para consultar la respuesta.
- `getClient()` para acceder al `ApiClient` subyacente.

Los setters y `addContent()` permiten encadenamiento. `setParams()` reemplaza el array completo. El paquete no implementa streaming. OpenAI, Claude y Grok realizan I/O remoto; Ollama llama al servicio local configurado en código.

Las respuestas suelen exponer `status`, `error` y `data`, pero los providers normalizan datos de modo diferente. `getContent(true)` intenta decodificar JSON sólo si encuentra un bloque de código Markdown etiquetado como `json` en OpenAI, Claude o Grok. Ollama devuelve `response` sin esa decodificación. Las rutas y significados de tokens y finalización varían por proveedor.

## Diferencias por provider

### OpenAI

`OpenAIProvider::exec()` valida el ID contra una lista interna y despacha modelos a chat completions, completions o generación de imágenes. El mapa también enumera IDs de audio, embeddings y moderación, pero esas rutas terminan en `ProviderException('Endpoint not implemented...')`; no se pueden ejecutar a través del dispatcher actual. `analyzeImage()` usa chat completions con `gpt-4o`. Los IDs codificados no garantizan que el proveedor los mantenga disponibles.

### Anthropic Claude

Envía una petición no streaming a `https://api.anthropic.com/v1/messages`, con headers `x-api-key` y `anthropic-version`. `exec()` acepta sólo IDs presentes en el mapa de fuente. `getContent()` extrae el primer bloque de texto y puede decodificar JSON dentro de un bloque cercado.

### xAI Grok

Envía una petición no streaming a `https://api.x.ai/v1/chat/completions`. No mantiene una lista local de IDs de modelo admitidos. Además del contrato común, expone getters para rol del mensaje, rechazo, nombre de modelo, ID de respuesta, fingerprint del sistema y detalles de tokens del prompt.

### Ollama

Usa `http://localhost:11434/api/generate` con `stream: false`. Se debe establecer un modelo local con `setModel()` o pasarlo a `exec($model)`; si falta, se lanza `ProviderException`. `addContent()` concatena el texto de mensajes en un prompt y no conserva sus roles en la solicitud. `listModels()` consulta `/api/tags`. El constructor no expone configuración de host o puerto.

## Errores y verificación

Los constructores de OpenAI, Claude y Grok lanzan `ProviderException` si no encuentran una clave. `exec()` también puede lanzarla para un provider o modelo no admitido. Los errores de API/transporte pueden aparecer en `status`, `error` y `error()`.

Se inspeccionaron contrato, factory, providers, configuración, manifiesto, README y el test de factory. No se ejecutó el test ni se hicieron llamadas a OpenAI, Anthropic, xAI u Ollama. Tampoco se validaron credenciales, modelos, instalación desde un consumidor Composer o descubrimiento del ServiceProvider.

## Relacionado

- [ApiClient](/core/api-client), usado por los providers.
- [API Clients](/integrations/api-clients) y [Shopify Connector](/integrations/shopify).
- [Microcore y paquetes](/core/microcore).
