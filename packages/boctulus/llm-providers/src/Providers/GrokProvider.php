<?php

namespace Boctulus\LLMProviders\Providers;

use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\LLMProviders\Contracts\LLMProviderInterface;
use Boctulus\LLMProviders\Exceptions\ProviderException;

/**
 * Proveedor para Grok AI (X.AI)
 *
 * Soporta los modelos de Grok desarrollados por xAI (X.AI)
 */
class GrokProvider implements LLMProviderInterface
{
    protected $api_key;
    protected $model = 'grok-beta';
    protected $messages = [];
    protected $params;
    protected $response;
    protected $error_msg;

    // API client
    public $client;

    /**
     * @param string|null $api_key API key de Grok (X.AI)
     */
    public function __construct(?string $api_key = null)
    {
        $this->api_key = $api_key ?? Config::get()['xai_api_key'] ?? null;

        if (empty($this->api_key)) {
            throw new ProviderException(
                'xai_api_key is required',
                0,
                null,
                'Grok'
            );
        }

        $this->client = ApiClient::instance()
            ->setHeaders([
                "Content-Type"  => "application/json",
                "Authorization" => "Bearer {$this->api_key}"
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
        if ($model === null) {
            $model = $this->model;
        }

        $endpoint = 'https://api.x.ai/v1/chat/completions';

        $data = [
            'model'    => $model,
            'messages' => $this->messages,
            'stream'   => false
        ];

        if (!empty($this->params)) {
            $data = array_merge($data, $this->params);
        }

        $this->client
            ->setBody($data)
            ->post($endpoint);

        $rawResponse = $this->client->getResponse();

        // Normalizar respuesta al formato estándar
        $this->response = [
            'status' => $rawResponse['http_code'] ?? 500,
            'error'  => $rawResponse['error'] ?? null,
            'data'   => $rawResponse['data'] ?? null
        ];

        // Manejar errores
        if (!empty($this->response['error'])) {
            $this->error_msg = $this->response['error'];
        }

        if ($this->response['status'] !== 200) {
            if (empty($this->error_msg)) {
                $this->error_msg = "HTTP error: " . $this->response['status'];
            }
        }

        // Si hay error en los datos
        if (isset($this->response['data']['error'])) {
            $this->error_msg = $this->response['data']['error'];
            $this->response['error'] = $this->response['data']['error'];
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
        return $this->response['data']['choices'][0]['finish_reason'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function wereTokenEnough(): bool
    {
        return ($this->getFinishReason() != 'length');
    }

    /**
     * @inheritDoc
     */
    public function isComplete(): bool
    {
        return ($this->getFinishReason() === 'stop');
    }

    /**
     * @inheritDoc
     */
    public function getContent(bool $decode = true)
    {
        if (!empty($this->error_msg)) {
            return false;
        }

        if (!isset($this->response['data']['choices'][0]['message']['content'])) {
            return null;
        }

        $content = $this->response['data']['choices'][0]['message']['content'];

        if ($decode) {
            if (preg_match('/```json\s*(.+?)\s*```/s', $content, $matches)) {
                $json_string = $matches[1];
                try {
                    $decoded = json_decode($json_string, true);
                    if ($decoded !== null) {
                        return $decoded;
                    }
                } catch (\Exception $e) {
                    // Si falla la decodificación, retornar el contenido original
                }
            }
        }

        return $content;
    }

    /**
     * Obtiene el rol del mensaje en la respuesta
     *
     * @return string|null
     */
    public function getMessageRole(): ?string
    {
        return $this->response['data']['choices'][0]['message']['role'] ?? null;
    }

    /**
     * Obtiene el rechazo si el modelo rechazó la solicitud
     *
     * @return string|null
     */
    public function getRefusal(): ?string
    {
        return $this->response['data']['choices'][0]['message']['refusal'] ?? null;
    }

    /**
     * Obtiene el nombre del modelo utilizado
     *
     * @return string|null
     */
    public function getModelName(): ?string
    {
        return $this->response['data']['model'] ?? null;
    }

    /**
     * Obtiene el ID de la respuesta
     *
     * @return string|null
     */
    public function getResponseId(): ?string
    {
        return $this->response['data']['id'] ?? null;
    }

    /**
     * Obtiene la huella del sistema
     *
     * @return string|null
     */
    public function getSystemFingerprint(): ?string
    {
        return $this->response['data']['system_fingerprint'] ?? null;
    }

    /**
     * Obtiene detalles de tokens del prompt
     *
     * @return array|null
     */
    public function getPromptTokensDetails(): ?array
    {
        return $this->response['data']['usage']['prompt_tokens_details'] ?? null;
    }
}
