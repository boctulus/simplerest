# LLM Providers

Package unificado para consumir APIs de distintos LLM (Large Language Models) con una interfaz consistente y extensible.

## Estado del Proyecto

**Versión**: 1.0.0
**Estado**: Producción (Funcional)
**Última actualización**: 2025-01

### Observaciones sobre Implementación y Capacidades

#### ✅ Ventajas del Package

1. **Interfaz Unificada**: Trabaja con múltiples proveedores LLM usando exactamente la misma API
2. **Código DRY**: Evita duplicación de lógica entre diferentes implementaciones de proveedores
3. **Fácil Migración**: Cambiar de un proveedor a otro requiere solo cambiar el nombre en el factory
4. **Extensibilidad**: Agregar nuevos proveedores es trivial (solo implementar la interfaz)
5. **Type-Safe**: Interfaces bien definidas con documentación completa
6. **Manejo Robusto de Errores**: Excepciones personalizadas con detalles específicos del proveedor
7. **Factory Pattern**: Instanciación simplificada y consistente
8. **Mantención Centralizada**: Bugs y mejoras se propagan a todos los proveedores

#### ⚠️ Limitaciones Actuales

1. **Funcionalidades Específicas**: Algunas características únicas de cada proveedor no están expuestas en la interfaz común
   - OpenAI: Análisis de imágenes, generación de audio (Whisper), DALL-E
   - Claude: Streaming de respuestas, tool use (function calling)
   - Grok: Métodos específicos como `getSystemFingerprint()`, `getPromptTokensDetails()`

2. **Respuestas Heterogéneas**: Aunque normalizadas, cada proveedor devuelve estructuras ligeramente diferentes
   - OpenAI: `finish_reason` puede ser 'stop', 'length', 'content_filter'
   - Claude: `stop_reason` puede ser 'end_turn', 'stop_sequence', 'max_tokens'
   - Esto está documentado pero requiere atención del desarrollador

3. **Testing Limitado**: No incluye tests automatizados (se recomienda agregar PHPUnit tests)

4. **Sin Soporte para Streaming**: Actualmente no soporta respuestas en streaming (importante para UX en tiempo real)

#### 💰 ¿Vale la Pena este package?

**SÍ, definitivamente vale la pena** si:

- ✅ Trabajas con múltiples proveedores de LLM
- ✅ Quieres flexibilidad para cambiar de proveedor sin reescribir código
- ✅ Prefieres una interfaz limpia y *consistente*
- ✅ Necesitas centralizar la lógica de manejo de errores
- ✅ Planeas agregar más proveedores en el futuro (Gemini, Mistral, etc.)
- ✅ Valoras código mantenible y testeable

**Considera alternativas** si:

- ❌ Solo usas un único proveedor y no planeas cambiar
- ❌ Necesitas funcionalidades muy específicas de un proveedor particular
- ❌ Requieres streaming de respuestas (por ahora)
- ❌ Tu proyecto es extremadamente simple (1-2 llamadas a API)

#### 🚀 Mejoras Futuras Recomendadas

1. Agregar soporte para streaming de respuestas
2. Implementar tests unitarios con PHPUnit
3. Agregar más proveedores (Gemini, Mistral, Llama, etc.)
4. Soporte para embeddings de manera unificada
5. Cache inteligente de respuestas con TTL configurable
6. Logging estructurado de peticiones/respuestas
7. Rate limiting configurable por proveedor
8. Retry automático con backoff exponencial

---

## Características

- **Interfaz unificada**: Trabaja con múltiples proveedores usando la misma API
- **Extensible**: Fácil de agregar nuevos proveedores
- **Factory Pattern**: Instanciación simplificada de providers
- **Type-safe**: Interfaces bien definidas y documentadas
- **Manejo de errores**: Excepciones personalizadas con información detallada

## Proveedores Soportados

| Proveedor | Alias | Estado | Modelos Principales |
|-----------|-------|--------|---------------------|
| **OpenAI** | `openai`, `chatgpt`, `gpt` | ✅ Completo | GPT-4, GPT-4o, GPT-3.5, DALL-E, Whisper |
| **Claude AI** | `claude`, `anthropic` | ✅ Completo | Claude 3 Opus/Sonnet/Haiku, Claude 2 |
| **Grok** | `grok`, `xai` | ✅ Completo | Grok Beta |
| **Ollama** | `ollama` | ✅ Completo | Modelos locales (Llama, Mistral, etc) |

## Instalación

```bash
composer require boctulus/llm-providers
```

The package declares SimpleRest as its Composer dependency. Run its offline contract suite with `composer test`.

## Configuración

Configura tus API keys en el archivo de configuración:

```php
// config/config.php
return [
    'openai_api_key' => 'sk-...',
    'claude_api_key' => 'sk-ant-...',
    'xai_api_key' => 'xai-...',
];
```

O mediante el archivo de configuración del package:

```php
// packages/boctulus/llm-providers/config/config.php
return [
    'default_provider' => 'openai',

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'default_model' => 'gpt-4o-mini',
        ],
        'claude' => [
            'api_key' => env('CLAUDE_API_KEY'),
            'api_version' => '2023-06-01',
            'default_model' => 'claude-3-sonnet-20240229',
        ],
        'grok' => [
            'api_key' => env('XAI_API_KEY'),
            'default_model' => 'grok-beta',
        ],
        'ollama' => [
            'default_model' => 'llama2', // O cualquier otro modelo que tengas
        ],
    ],
];
```

## Uso Básico

### Usando el Factory

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

// Crear proveedor de OpenAI
$llm = LLMFactory::create('openai');

// Crear proveedor de Claude
$llm = LLMFactory::create('claude');

// Crear proveedor de Grok
$llm = LLMFactory::create('grok');

// Crear proveedor de Ollama
$llm = LLMFactory::create('ollama');

// O usar métodos estáticos directos
$openai = LLMFactory::openai();
$claude = LLMFactory::claude();
$grok   = LLMFactory::grok();
$ollama = LLMFactory::ollama();

// Con API key personalizada
$llm = LLMFactory::create('openai', ['api_key' => 'sk-...']);
```

### Ejemplo con OpenAI

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

// Crear instancia
$llm = LLMFactory::openai();

// Configurar modelo y parámetros
$llm->setModel('gpt-4o-mini')
    ->setMaxTokens(500)
    ->setTemperature(0.7);

// Agregar contenido
$llm->addContent('¿Cuál es la capital de Francia?');

// Ejecutar
$response = $llm->exec();

// Obtener contenido de la respuesta
if ($response['status'] == 200) {
    echo $llm->getContent();

    // Información adicional
    $tokens = $llm->getTokenUsage();
    $isComplete = $llm->isComplete();
}
```

### Ejemplo con Claude AI

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

$llm = LLMFactory::claude();

$llm->setModel('claude-3-sonnet-20240229')
    ->setParams(['max_tokens' => 200]);

$llm->addContent('Explica la teoría de la relatividad en términos simples');

$response = $llm->exec();

if ($response['status'] == 200) {
    echo $llm->getContent();

    // Verificar si la respuesta está completa
    if ($llm->isComplete()) {
        echo "\n✅ Respuesta completa";
    }
}
```

### Ejemplo con Grok

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

$llm = LLMFactory::grok();

$llm->setModel('grok-beta')
    ->setTemperature(0.5)
    ->setMaxTokens(300);

$llm->addContent('¿Qué es la computación cuántica?');

$response = $llm->exec();

if ($response['status'] == 200) {
    echo $llm->getContent();

    // Métodos específicos de Grok
    echo "\nModel: " . $llm->getModelName();
    echo "\nResponse ID: " . $llm->getResponseId();
}
```

### Ejemplo con Ollama

```php
use Boctulus\LLMProviders\Factory\LLMFactory;

$llm = LLMFactory::ollama();

// Listar modelos locales
$models = $llm->listModels();
print_r($models);

// Usar un modelo específico
$llm->setModel('llama2') // o 'mistral', 'codellama', etc.
    ->addContent('Escribe un poema sobre la programación');

$response = $llm->exec();

if ($response['status'] == 200) {
    echo $llm->getContent();
}
```

### Conversaciones Multi-turno

```php
$llm = LLMFactory::openai();

// Agregar contexto del sistema
$llm->addContent('Eres un asistente experto en física.', 'system');

// Agregar mensaje del usuario
$llm->addContent('¿Qué es un agujero negro?', 'user');

// Ejecutar
$response = $llm->exec();
$respuesta1 = $llm->getContent();

// Continuar la conversación
$llm->addContent('¿Y cómo se forma?', 'user');
$response = $llm->exec();
$respuesta2 = $llm->getContent();
```

### Extracción de JSON

```php
$llm = LLMFactory::openai();

$llm->addContent('
    Dame una lista de 3 países europeos con sus capitales en formato JSON.
    Devuelve el JSON dentro de bloques ```json
');

$response = $llm->exec();

// getContent(true) automáticamente decodifica JSON
$data = $llm->getContent(true);

// $data ahora es un array PHP
print_r($data);
/*
Array (
    [0] => Array (
        [pais] => Francia
        [capital] => París
    )
    ...
)
*/
```

### Cambiar de Proveedor Fácilmente

```php
// Esta función funciona con CUALQUIER proveedor
function askLLM($providerName, $question) {
    $llm = LLMFactory::create($providerName);
    $llm->addContent($question);
    $response = $llm->exec();

    return $llm->getContent();
}

// Usar diferentes proveedores
$respuesta1 = askLLM('openai', '¿Qué es PHP?');
$respuesta2 = askLLM('claude', '¿Qué es PHP?');
$respuesta3 = askLLM('grok', '¿Qué es PHP?');

// Comparar respuestas de diferentes LLMs
```

## Métodos de la Interfaz

### Configuración

- `setModel(string $name)`: Configura el modelo a usar
- `getModel(): string`: Obtiene el modelo actual
- `setMaxTokens(int $val)`: Configura el límite de tokens
- `getMaxTokens(): ?int`: Obtiene el límite de tokens
- `setTemperature(float $val)`: Configura la temperatura (0.0 - 2.0)
- `setParams(array $params)`: Configura parámetros adicionales

### Contenido

- `addContent(string $content, string $role = 'user')`: Agrega un mensaje
- `exec(?string $model = null): array`: Ejecuta la solicitud

### Respuesta

- `getContent(bool $decode = true)`: Obtiene el contenido de la respuesta
- `getTokenUsage(): ?array`: Obtiene información de uso de tokens
- `getFinishReason(): ?string`: Obtiene la razón de finalización
- `isComplete(): bool`: Verifica si la respuesta está completa
- `wereTokenEnough(): bool`: Verifica si hubo suficientes tokens
- `error()`: Obtiene el mensaje de error si lo hay

### Cliente

- `getClient()`: Obtiene el cliente HTTP subyacente

## Modelos Soportados

### OpenAI

#### Recomendados
- `gpt-4o-mini` ⭐ (económico, excelente relación calidad-precio)
- `gpt-4o` (potente, con capacidad de visión)
- `gpt-4` (muy potente, costoso)

#### Otros
- Serie O1: `o1-preview`, `o1-mini` (razonamiento avanzado)
- Serie GPT-3.5: `gpt-3.5-turbo-1106` (ahora más caro que gpt-4o-mini)
- Imágenes: `dall-e-3`, `dall-e-2`
- Audio: `whisper-1`, `tts-1`, `tts-1-hd`

### Claude AI

- `claude-3-opus-20240229` (más potente, costoso)
- `claude-3-sonnet-20240229` ⭐ (balance ideal)
- `claude-3-haiku-20240307` (más rápido, económico)
- `claude-2.1`, `claude-2.0` (versiones anteriores)
- `claude-instant-1.2` (rápido y económico)

### Grok (X.AI)

- `grok-beta` (modelo principal)

### Ollama

Cualquier modelo instalado localmente, por ejemplo:
- `llama2`
- `mistral`
- `codellama`
- `phi`

## Manejo de Errores

```php
use Boctulus\LLMProviders\Exceptions\ProviderException;

try {
    $llm = LLMFactory::create('openai');
    $llm->addContent('Test');
    $response = $llm->exec();

    // Verificar errores de la API
    if ($error = $llm->error()) {
        echo "Error del proveedor: ";
        print_r($error);
    }

    // Verificar respuesta HTTP
    if ($response['status'] !== 200) {
        echo "Error HTTP: " . $response['status'];
    }

} catch (ProviderException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Provider: " . $e->getProvider() . "\n";
    print_r($e->getDetails());
}
```

### Razones de Finalización

Cada proveedor usa diferentes códigos para indicar por qué terminó la generación:

#### OpenAI / Grok
- `stop`: Respuesta completa y natural ✅
- `length`: Se alcanzó el límite de tokens ⚠️
- `content_filter`: Bloqueado por filtros de contenido 🚫

#### Claude
- `end_turn`: Respuesta completa ✅
- `stop_sequence`: Encontró secuencia de parada ✅
- `max_tokens`: Se alcanzó el límite de tokens ⚠️

```php
$response = $llm->exec();

$finishReason = $llm->getFinishReason();

if ($llm->isComplete()) {
    echo "✅ Respuesta completa";
} else if (!$llm->wereTokenEnough()) {
    echo "⚠️ Se acabaron los tokens, aumenta max_tokens";
} else {
    echo "🚫 Respuesta bloqueada o interrumpida";
}
```

## Agregar Nuevos Proveedores

Para agregar un nuevo proveedor (por ejemplo, Google Gemini):

### 1. Crear el Provider

```php
// src/Providers/GeminiProvider.php
<?php

namespace Boctulus\LLMProviders\Providers;

use Boctulus\LLMProviders\Contracts\LLMProviderInterface;

class GeminiProvider implements LLMProviderInterface
{
    // Implementar todos los métodos de la interfaz
    public function exec(?string $model = null): array {
        // Lógica específica de Gemini
    }

    // ... resto de métodos
}
```

### 2. Registrar en el Factory

```php
// src/Factory/LLMFactory.php

const PROVIDERS = [
    // ... proveedores existentes
    'gemini' => GeminiProvider::class,
    'google' => GeminiProvider::class,
];

// Agregar en instantiateProvider()
case GeminiProvider::class:
    $apiKey = $config['api_key'] ?? null;
    return new GeminiProvider($apiKey);

// Método helper opcional
public static function gemini(?string $apiKey = null): GeminiProvider
{
    return new GeminiProvider($apiKey);
}
```

### 3. Usar el nuevo provider

```php
$llm = LLMFactory::create('gemini');
$llm->addContent('Hello Gemini!');
$response = $llm->exec();
```

## Arquitectura del Package

```
packages/boctulus/llm-providers/
├── src/
│   ├── Contracts/
│   │   └── LLMProviderInterface.php    # Interfaz común
│   ├── Providers/
│   │   ├── OpenAIProvider.php          # Implementación OpenAI
│   │   ├── ClaudeProvider.php          # Implementación Claude
│   │   ├── GrokProvider.php            # Implementación Grok
│   │   └── OllamaProvider.php          # Implementación Ollama
│   ├── Factory/
│   │   └── LLMFactory.php              # Factory pattern
│   ├── Exceptions/
│   │   └── ProviderException.php       # Excepciones personalizadas
│   └── ServiceProvider.php             # Service provider
├── config/
│   └── config.php                      # Configuración
├── composer.json
└── README.md
```

## Testing (Recomendado)

Aunque no incluye tests por defecto, se recomienda agregar:

```php
// tests/OpenAIProviderTest.php
use PHPUnit\Framework\TestCase;
use Boctulus\LLMProviders\Factory\LLMFactory;

class OpenAIProviderTest extends TestCase
{
    public function testBasicCompletion()
    {
        $llm = LLMFactory::openai();
        $llm->addContent('Say "test"');
        $response = $llm->exec();

        $this->assertEquals(200, $response['status']);
        $this->assertIsString($llm->getContent());
    }
}
```

## Troubleshooting

### API Key no encontrada

```
ProviderException: openai_api_key is required
```

**Solución**: Configura la API key en `config/config.php` o pásala al constructor:

```php
$llm = LLMFactory::create('openai', ['api_key' => 'sk-...']);
```

### Respuesta vacía

Si `getContent()` retorna `null`, verifica:

```php
$response = $llm->exec();

// 1. Verificar status HTTP
if ($response['status'] !== 200) {
    echo "Error HTTP: " . $response['status'];
}

// 2. Verificar errores del proveedor
if ($error = $llm->error()) {
    echo "Error: ";
    print_r($error);
}

// 3. Verificar estructura de respuesta
print_r($response['data']);
```

### Tokens insuficientes

```php
if (!$llm->wereTokenEnough()) {
    echo "Aumenta max_tokens";
    $llm->setMaxTokens(2000); // Ajustar
}
```

## Comparación de Costos (Aproximados)

| Proveedor | Modelo | Input (1M tokens) | Output (1M tokens) |
|-----------|--------|-------------------|-------------------|
| OpenAI | gpt-4o-mini | $0.15 | $0.60 |
| OpenAI | gpt-4o | $2.50 | $10.00 |
| OpenAI | gpt-4 | $30.00 | $60.00 |
| Claude | haiku | $0.25 | $1.25 |
| Claude | sonnet | $3.00 | $15.00 |
| Claude | opus | $15.00 | $75.00 |
| Grok | grok-beta | TBD | TBD |

*Precios aproximados al 2025-01, sujetos a cambio*

## Contribuir

Para contribuir al package:

1. Implementa nuevos proveedores siguiendo la interfaz `LLMProviderInterface`
2. Agrega tests para tus providers
3. Actualiza la documentación
4. Documenta modelos soportados y sus capacidades

## Licencia

MIT License

## Autor

**boctulus**

---

## Changelog

### v1.0.0 (2025-01)

- ✅ Implementación inicial con OpenAI, Claude y Grok
- ✅ Factory pattern para instanciación
- ✅ Interfaz unificada LLMProviderInterface
- ✅ Manejo robusto de errores con ProviderException
- ✅ Soporte para extracción automática de JSON
- ✅ Documentación completa
- ✅ Ejemplos de uso para cada proveedor
