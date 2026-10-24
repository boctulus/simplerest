# Proveedores LLM

`boctulus/simplerest-llm-providers` es un paquete opcional fuera del core. Su interfaz común cubre OpenAI, Anthropic Claude, xAI Grok y Ollama, con diferencias importantes entre sus respuestas, parámetros y configuración. Esta referencia describe el código de `packages/boctulus/simplerest-llm-providers`; no afirma que una credencial funcione, que un modelo siga disponible o que una petición externa se haya ejecutado.

## Paquete y carga

El manifiesto Composer declara PHP `>=8.1,<8.5` y `boctulus/simplerest ^2.0 || ^3.0`, con PSR-4 `Boctulus\LLMProviders\` hacia `src/`. Declara `Boctulus\LLMProviders\ServiceProvider` en `extra.simplerest.providers`, pero la integración automática del consumidor no se verificó. El `ServiceProvider` de este paquete incluye `config/cli_routes.php` en `boot()`; no carga `config/config.php` ni registra `LLMFactory` en el contenedor.

La configuración del paquete es sólo una referencia. `LLMFactory::create()` no la carga: toma los valores de su argumento y los pasa a los constructores. OpenAI, Claude y Grok pueden recurrir a `Config::get()` (`openai_api_key`, `claude_api_key`, `xai_api_key`); Ollama no usa clave. El archivo de referencia, su comentario sobre providers disponibles y el ejemplo del README no coinciden por completo con la factory. El detalle está registrado en el [ledger de auditoría](../audit/README.md#llm-provider-configuration-divergence-2026-10-24).

## Crear una instancia

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

$llm = LLMFactory::create('openai', [
    'api_key' => getenv('OPENAI_API_KEY'),
]);

$llm->setModel('gpt-4o-mini')
    ->setParams(['max_tokens' => 400, 'temperature' => 0.4])
    ->addContent('Resume el propósito de esta aplicación.');

// exec() envía una petición al proveedor configurado.
// $response = $llm->exec();
```

`create()` normaliza el nombre con `trim()` y minúsculas. Los alias aceptados en esta revisión del código son:

| Provider | Alias | Modelo inicial en el constructor | Credencial/ubicación usada por el constructor |
| --- | --- | --- | --- |
| OpenAI | `openai`, `chatgpt`, `gpt` | `gpt-4o-mini` | `api_key` explícita o `Config::get()['openai_api_key']` |
| Anthropic | `claude`, `anthropic` | `claude-sonnet-4-6` | `api_key` explícita o `Config::get()['claude_api_key']`; versión API `2023-06-01` si no se pasa otra |
| xAI | `grok`, `xai` | `grok-4.7` | `api_key` explícita o `Config::get()['xai_api_key']` |
| Ollama | `ollama` | Vacío; debe configurarse antes de `exec()` | No usa API key; endpoint local fijo `http://localhost:11434` |

`LLMFactory::create($provider, $config)` sólo lee `api_key` y, para Claude, `api_version`. No consume `default_provider`, `providers[*].default_model` ni `default_params` del archivo de referencia. El array de configuración pasado a `create('ollama', ...)` se ignora. Las claves indicadas aquí reflejan búsquedas de código, no configuración presente en todas las aplicaciones.

## Interfaz común

`LLMProviderInterface` define:

- `setModel()` / `getModel()` para elegir el identificador de modelo.
- `setParams()` para reemplazar el array de parámetros, `setMaxTokens()` / `getMaxTokens()` y `setTemperature()` como atajos.
- `addContent($content, $role = 'user')` para añadir mensajes; cada llamada acumula un elemento en la conversación de la instancia.
- `exec(?string $model = null)` para enviar la solicitud y devolver un array de respuesta.
- `getContent()`, `getTokenUsage()`, `getFinishReason()`, `isComplete()`, `wereTokenEnough()` y `error()` para consultar el resultado.
- `getClient()` para acceder al `ApiClient` subyacente.

Los métodos de modelo, parámetros y mensajes devuelven la instancia para encadenarlos. `setParams()` asigna el array completo; no combina con los parámetros anteriores. `exec()` realiza I/O de red para OpenAI, Claude y Grok. Ollama usa la API local. El paquete no implementa streaming.

Las respuestas se representan normalmente con `status`, `error` y `data`, aunque la normalización interna difiere por proveedor. `getContent(true)` intenta decodificar JSON sólo cuando encuentra un bloque de código Markdown con etiqueta `json` en OpenAI, Claude o Grok. Ollama devuelve el campo `response` y no aplica esa decodificación. `getTokenUsage()` y `getFinishReason()` leen rutas distintas, y sus nombres no garantizan semántica idéntica entre proveedores.

## Diferencias por proveedor

### OpenAI

`OpenAIProvider` tiene un conjunto de IDs de modelo codificado en `exec()`, no una aceptación general de cualquier modelo de la cuenta. El dispatcher actual conecta modelos asignados a chat completions, completions y generación de imágenes. Declara constantes y asignaciones para audio, embeddings y moderación, pero esas ramas caen en `ProviderException('Endpoint not implemented...')`. La discrepancia de capacidad está anotada en el [ledger](../audit/README.md#llm-provider-configuration-divergence-2026-10-24).

El método `analyzeImage()` prepara una imagen y usa `gpt-4o` mediante chat completions. Los modelos aceptados y los defaults pertenecen al snapshot de este código; no son una garantía de disponibilidad actual en OpenAI.

### Anthropic Claude

`ClaudeProvider` envía una petición no streaming a `https://api.anthropic.com/v1/messages`, con los headers `x-api-key` y `anthropic-version`. `exec()` acepta sólo los IDs incluidos en su mapa fuente (entre ellos `claude-sonnet-4-6` y varios IDs anteriores). `getContent()` extrae el primer bloque de texto y puede convertir JSON dentro de un bloque cercado.

### xAI Grok

`GrokProvider` envía una petición no streaming a `https://api.x.ai/v1/chat/completions`. No valida el ID del modelo contra una lista local. Además del contrato común, expone getters para rol, rechazo, nombre de modelo, ID de respuesta, fingerprint del sistema y detalles de tokens del prompt.

### Ollama

`OllamaProvider` usa `http://localhost:11434/api/generate` y fija `stream: false`. Debe establecerse un modelo local con `setModel()` o pasarlo a `exec($model)`; si falta, lanza `ProviderException`. `addContent()` concatena el texto de todos los mensajes como un prompt y no conserva sus roles en la solicitud. `listModels()` consulta `/api/tags`. El constructor no expone host/puerto ni toma una configuración de la factory.

## Errores y límites de esta referencia

Los constructores remotos lanzan `ProviderException` si no encuentran la clave requerida. `exec()` puede lanzar esa excepción por provider o modelo no admitido; los errores del transporte/API también pueden reflejarse en `status`, `error` y `error()`. Comprueba ambas vías según el flujo de tu aplicación.

Se inspeccionaron el contrato, factory, providers, configuración, manifiesto, README y el test de factory. El test no se ejecutó. No se hicieron solicitudes a OpenAI, Anthropic, xAI u Ollama, ni se validaron credenciales, disponibilidad de modelos, caché efectiva, instalación desde un consumidor Composer o descubrimiento del ServiceProvider.

## Relacionado

- [ApiClient del core](../core/api-client.md), usado por los providers.
- [API Clients opcionales](api-clients.md) y [Shopify Connector](shopify.md), otras integraciones auditadas.
- [Límites de paquetes Composer](../architecture/composer-packages.md) y [Proceso de publicación](../architecture/composer-release-process.md).
