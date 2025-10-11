<?php

namespace Boctulus\LLMProviders\Providers;

use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\LLMProviders\Contracts\LLMProviderInterface;
use Boctulus\LLMProviders\Exceptions\ProviderException;

class OllamaProvider implements LLMProviderInterface
{
protected string $model = '';
protected array $params = [];
protected array $messages = [];
protected ?array $response = null;
protected ?string $error_msg = null;

public $client;

const DEFAULT_PORT = 11434;

public function __construct()
{
    $this->client = ApiClient::instance()
        ->setBaseUrl('http://localhost:' . self::DEFAULT_PORT)
        ->setHeaders([
            'Content-Type' => 'application/json'
        ])
        ->disableSSL()
        ->enablePostRequestCache()
        ->decode();
}

public function getClient()
{
    return $this->client;
}

public function setModel(string $name)
{
    $this->model = $name;
    return $this;
}

public function getModel(): string
{
    return $this->model;
}

public function setParams(array $params)
{
    $this->params = $params;
    return $this;
}

public function setMaxTokens(int $val)
{
    $this->params['max_tokens'] = $val;
    return $this;
}

public function getMaxTokens(): ?int
{
    return $this->params['max_tokens'] ?? null;
}

public function setTemperature(float $val)
{
    $this->params['temperature'] = $val;
    return $this;
}

public function addContent(string $content, string $role = 'user')
{
    $this->messages[] = [
        'role' => $role,
        'content' => $content
    ];
    return $this;
}

public function exec(?string $model = null): array
{
    $modelToUse = $model ?? $this->model;
    if (empty($modelToUse)) {
        throw new ProviderException(
            "Model must be set before executing",
            0,
            null,
            'Ollama'
        );
    }

    $prompt = '';
    foreach ($this->messages as $msg) {
        $prompt .= $msg['content'] . "\n";
    }

    $data = array_merge([
        'model' => $modelToUse,
        'prompt' => $prompt,
        'stream' => false,  // Deshabilitar streaming para obtener respuesta completa
    ], $this->params);

    try {
        $this->client->setBody($data)->post('/api/generate');

        $this->response = [
            'status' => $this->client->getStatus(),
            'error' => $this->client->getError(),
            'data' => $this->client->getResponse()
        ];

        if (is_string($this->response['data'])) {
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        if (isset($this->response['data']['error'])) {
            $this->error_msg = $this->response['data']['error'];
        }

    } catch (\Exception $e) {
        throw new ProviderException(
            "Error connecting to Ollama API: " . $e->getMessage(),
            0,
            $e,
            'Ollama'
        );
    }

    return $this->response;
}

public function getContent(bool $decode = true)
{
    if (!empty($this->error_msg)) return false;

    // Con stream: false, Ollama devuelve un solo JSON con el campo 'response'
    // ApiClient wraps the response in data->data
    if (isset($this->response['data']['data']['response'])) {
        return $this->response['data']['data']['response'];
    }

    return null;
}

public function getTokenUsage(): ?array
{
    return $this->response['data']['usage'] ?? null;
}

public function getFinishReason(): ?string
{
    return $this->response['data']['stop_reason'] ?? null;
}

public function isComplete(): bool
{
    return $this->getFinishReason() === 'stop';
}

public function wereTokenEnough(): bool
{
    return $this->getFinishReason() !== 'max_tokens';
}

public function error()
{
    return $this->error_msg;
}

/**
 * Lista los modelos instalados localmente en Ollama
 *
 * @return array
 */
public function listModels(): array
{
    try {
        $this->client->get('/api/tags');
        $resp = $this->client->getResponse();

        // Procesar la respuesta
        if (is_array($resp) && isset($resp['data'])) {
            $data = $resp['data'];
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            return $data['models'] ?? [];
        }

        if (is_string($resp)) {
            $resp = json_decode($resp, true);
        }

        return $resp['models'] ?? [];
    } catch (\Exception $e) {
        throw new ProviderException(
            "Error fetching models from Ollama: " . $e->getMessage(),
            0,
            $e,
            'Ollama'
        );
    }
}


}
