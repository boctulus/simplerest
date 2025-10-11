<?php

namespace Boctulus\LLMProviders\Providers;

use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\LLMProviders\Contracts\LLMProviderInterface;
use Boctulus\LLMProviders\Exceptions\ProviderException;

/**
 * Proveedor para Claude AI (Anthropic)
 *
 * Soporta los modelos de Claude incluyendo:
 * - Claude 3 Opus
 * - Claude 3 Sonnet
 * - Claude 3 Haiku
 * - Claude 2.1
 * - Claude 2.0
 * - Claude Instant 1.2
 */
class ClaudeProvider implements LLMProviderInterface
{
    const DEFAULT_API_VERSION = '2023-06-01';

    protected $api_key;
    protected $api_version;
    protected $messages = [];
    protected $response;
    protected $params;
    protected $error_msg;

    protected $model = 'claude-3-sonnet-20240229';

    // API client
    public $client;

    const MESSAGES = 1;

    /**
     * @param string|null $api_key API key de Claude
     * @param string|null $api_version Versión de la API
     */
    public function __construct(?string $api_key = null, ?string $api_version = null)
    {
        $this->api_key = $api_key ?? Config::get()['claude_api_key'] ?? null;

        if (empty($this->api_key)) {
            throw new ProviderException(
                'claude_api_key is required',
                0,
                null,
                'Claude'
            );
        }

        $this->api_version = $api_version ?? self::DEFAULT_API_VERSION;

        $this->client = ApiClient::instance()
            ->setHeaders([
                "Content-type" => "application/json",
                "x-api-key" => $this->api_key,
                "anthropic-version" => $this->api_version
            ])
            ->disableSSL()
            ->enablePostRequestCache()
            ->decode();
    }

    /**
     * @inheritDoc
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function setModel(string $name)
    {
        $this->model = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    public function setMaxTokens(int $val)
    {
        $this->params['max_tokens'] = $val;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxTokens(): ?int
    {
        return $this->params['max_tokens'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setTemperature(float $val)
    {
        $this->params['temperature'] = $val;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addContent(string $content, string $role = 'user')
    {
        $this->messages[] = [
            'role'    => $role,
            'content' => $content
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParams(array $arr)
    {
        $this->params = $arr;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function error()
    {
        return $this->error_msg;
    }

    /**
     * @inheritDoc
     */
    public function exec(?string $model = null): array
    {
        $model_endpoints = [
            'claude-3-opus-20240229'   => static::MESSAGES,
            'claude-3-sonnet-20240229' => static::MESSAGES,
            'claude-3-haiku-20240307'  => static::MESSAGES,
            'claude-2.1'               => static::MESSAGES,
            'claude-2.0'               => static::MESSAGES,
            'claude-instant-1.2'       => static::MESSAGES,
        ];

        if (empty($model)) {
            $model = $this->model;
        }

        if (!array_key_exists($model, $model_endpoints)) {
            throw new ProviderException(
                "Model '$model' is not supported",
                0,
                null,
                'Claude',
                ['model' => $model]
            );
        }

        return $this->execMessages($model);
    }

    /**
     * Ejecuta una solicitud de mensajes a Claude
     *
     * @param string $model
     * @return array
     */
    protected function execMessages(string $model): array
    {
        $endpoint = 'https://api.anthropic.com/v1/messages';

        $data = [
            'model'    => $model,
            'messages' => $this->messages
        ];

        if (!empty($this->params)) {
            $data = array_merge($data, $this->params);
        }

        $this->client
            ->setBody($data)
            ->post($endpoint);

        $this->response = [
            'status' => $this->client->getStatus(),
            'error'  => $this->client->getError(),
            'data'   => $this->client->getResponse()
        ];

        if (is_string($this->response['data'])) {
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        // Extraer el contenido si está presente
        if (isset($this->response['data']['content'])) {
            // Mantener la estructura completa pero marcar que tiene contenido
            $this->response['data']['_has_content'] = true;
        }

        if (isset($this->response['data']['error'])) {
            $this->error_msg = $this->response['data']['error'];
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }

        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function getTokenUsage(): ?array
    {
        return $this->response['data']['usage'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getFinishReason(): ?string
    {
        return $this->response['data']['stop_reason'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function wereTokenEnough(): bool
    {
        $stopReason = $this->getFinishReason();
        return $stopReason !== 'max_tokens';
    }

    /**
     * @inheritDoc
     */
    public function isComplete(): bool
    {
        $stopReason = $this->getFinishReason();
        return in_array($stopReason, ['end_turn', 'stop_sequence']);
    }

    /**
     * @inheritDoc
     */
    public function getContent(bool $decode = true)
    {
        if (!empty($this->error_msg)) {
            return false;
        }

        // Claude devuelve el contenido en un array de bloques
        $content = $this->response['data']['content'][0]['text'] ?? null;

        if ($decode && $content !== null) {
            // Intentar extraer JSON si está presente
            if (preg_match('/```json\s*(.+?)\s*```/s', $content, $matches)) {
                $json_string = $matches[1];
                $decoded = json_decode($json_string, true);
                if ($decoded !== null) {
                    $content = $decoded;
                }
            }
        }

        return $content;
    }
}
