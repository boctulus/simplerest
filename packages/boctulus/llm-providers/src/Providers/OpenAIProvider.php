<?php

namespace Boctulus\LLMProviders\Providers;

use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\LLMProviders\Contracts\LLMProviderInterface;
use Boctulus\LLMProviders\Exceptions\ProviderException;

/**
 * Proveedor para OpenAI (ChatGPT)
 *
 * Soporta múltiples modelos y endpoints de OpenAI incluyendo:
 * - Chat Completions (GPT-4, GPT-3.5, etc.)
 * - Completions
 * - DALL-E (Generación de imágenes)
 * - Whisper (Audio)
 * - Embeddings
 * - Moderación
 */
class OpenAIProvider implements LLMProviderInterface
{
    protected $api_key;
    protected $model = 'gpt-4o-mini';
    protected $messages = [];
    protected $params;
    protected $response;
    protected $error_msg;
    protected $dynamic_token_usage = false;
    protected $dynamic_res_lenght = false;

    // API client
    public $client;

    const COMPLETIONS = 1;
    const CHAT_COMPLETIONS = 2;
    const MODERATION = 10;
    const IMAGES = 20;
    const AUDIO = 30;
    const EMBEDDINGS = 40;

    /**
     * @param string|null $api_key API key de OpenAI
     */
    public function __construct(?string $api_key = null)
    {
        $this->api_key = $api_key ?? Config::get()['openai_api_key'] ?? null;

        if (empty($this->api_key)) {
            throw new ProviderException(
                'openai_api_key is required',
                0,
                null,
                'OpenAI'
            );
        }

        $this->client = ApiClient::instance()
            ->setHeaders([
                "Content-type"  => "application/json",
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
     * Habilita el uso dinámico de tokens
     *
     * @return self
     */
    public function dynamicTokenUsage()
    {
        $this->dynamic_token_usage = true;
        return $this;
    }

    /**
     * Habilita la longitud de respuesta dinámica
     *
     * @return self
     */
    public function dynamicResponseLenght()
    {
        $this->dynamic_res_lenght = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addContent($content, string $role = 'user')
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
            // Modelos O1
            'o1-preview'               => static::CHAT_COMPLETIONS,
            'o1-preview-2024-09-12'    => static::CHAT_COMPLETIONS,
            'o1-mini'                  => static::CHAT_COMPLETIONS,
            'o1-mini-2024-09-12'       => static::CHAT_COMPLETIONS,

            // Modelos GPT-4
            'gpt-4o-mini'              => static::CHAT_COMPLETIONS,
            'gpt-4o'                   => static::CHAT_COMPLETIONS,
            'gpt-4o-2024-08-06'        => static::CHAT_COMPLETIONS,
            'gpt-4'                    => static::CHAT_COMPLETIONS,
            'gpt-4-1106-preview'       => static::COMPLETIONS,
            'gpt-4-0613'               => static::COMPLETIONS,
            'gpt-4-32k'                => static::COMPLETIONS,
            'gpt-4-32k-0613'           => static::COMPLETIONS,

            // Modelos GPT-3.5
            'gpt-3.5-turbo-1106'       => static::CHAT_COMPLETIONS,
            'gpt-3.5-turbo'            => static::COMPLETIONS,
            'gpt-3.5-turbo-16k'        => static::COMPLETIONS,
            'gpt-3.5-turbo-instruct'   => static::COMPLETIONS,

            // Modelos DALL·E
            'dall-e-3'                 => static::IMAGES,
            'dall-e-2'                 => static::IMAGES,

            // Modelos TTS (Text-to-Speech)
            'tts-1'                    => static::AUDIO,
            'tts-1-hd'                 => static::AUDIO,

            // Modelo Whisper (Reconocimiento de voz)
            'whisper-1'                => static::AUDIO,

            // Modelos de Embeddings
            'text-embedding-ada-002'   => static::EMBEDDINGS,

            // Modelos de Moderación
            'text-moderation-latest'   => static::MODERATION,
            'text-moderation-stable'   => static::MODERATION,

            // Modelos GPT base
            'babbage-002'              => static::COMPLETIONS,
            'davinci-002'              => static::COMPLETIONS,

            // Modelos GPT-3 Legacy
            'text-curie-001'           => static::COMPLETIONS,
            'text-babbage-001'         => static::COMPLETIONS,
            'text-ada-001'             => static::COMPLETIONS,
            'davinci'                  => static::COMPLETIONS,
            'curie'                    => static::COMPLETIONS,
            'babbage'                  => static::COMPLETIONS,
            'ada'                      => static::COMPLETIONS,
        ];

        if ($model === null) {
            $model = $this->model;
        }

        if (!isset($model_endpoints[$model])) {
            throw new ProviderException(
                "Model '$model' is not supported",
                0,
                null,
                'OpenAI',
                ['model' => $model]
            );
        }

        $endpoint = $model_endpoints[$model];

        switch ($endpoint) {
            case static::CHAT_COMPLETIONS:
                return $this->execChatCompletion($model);

            case static::COMPLETIONS:
                return $this->execCompletion($model);

            case static::IMAGES:
                return $this->execImageGeneration($model);

            default:
                throw new ProviderException(
                    "Endpoint not implemented for model '$model'",
                    0,
                    null,
                    'OpenAI',
                    ['model' => $model, 'endpoint' => $endpoint]
                );
        }
    }

    /**
     * Ejecuta una solicitud de chat completion
     *
     * @param string $model
     * @return array
     */
    protected function execChatCompletion(string $model): array
    {
        $endpoint = 'https://api.openai.com/v1/chat/completions';

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

        if (isset($this->response['data']['data'])) {
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])) {
            $this->error_msg         = $this->response['data']['error'];
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }

        return $this->response;
    }

    /**
     * Ejecuta una solicitud de completion
     *
     * @param string $model
     * @return array
     */
    protected function execCompletion(string $model): array
    {
        $endpoint = "https://api.openai.com/v1/engines/$model/completions";

        $data = [
            'prompt' => $this->messages[0]['content']
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

        if (isset($this->response['data']['data'])) {
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])) {
            $this->error_msg         = $this->response['data']['error'];
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }

        return $this->response;
    }

    /**
     * Ejecuta una solicitud de generación de imagen
     *
     * @param string $model
     * @return array
     */
    protected function execImageGeneration(string $model): array
    {
        $endpoint = 'https://api.openai.com/v1/images/generations';

        $data = [
            'model'  => $model,
            'prompt' => $this->messages[0]['content']
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

        if (isset($this->response['data']['data'])) {
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])) {
            $this->error_msg         = $this->response['data']['error'];
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
        return ($this->getFinishReason() == 'stop');
    }

    /**
     * @inheritDoc
     */
    public function getContent(bool $decode = true)
    {
        if (!empty($this->error_msg)) {
            return false;
        }

        $content = $this->response['data']['choices'][0]['message']['content'] ?? null;

        if ($decode && $content !== null) {
            if (preg_match('/```json\s*(.+?)\s*```/s', $content, $matches)) {
                $json_string = $matches[1];
                $content = json_decode($json_string, true);
            }
        }

        return $content;
    }

    /**
     * Analiza una imagen mediante modelos con capacidad de visión
     *
     * @param string $image_url URL o base64 de la imagen
     * @return string|false
     */
    public function analyzeImage(string $image_url)
    {
        $messages = [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Describe the image',
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => 'data:image/jpeg;base64,' . $image_url,
                            'detail' => 'high'
                        ],
                    ],
                ],
            ],
        ];

        foreach ($messages as $message) {
            $this->addContent($message, $message['role']);
        }

        $params = [
            'temperature' => 0.5,
            'max_tokens' => 300
        ];

        $this->setParams($params);

        $response = $this->exec('gpt-4o');

        if ($response['status'] == 200) {
            return $response['data']['choices'][0]['message']['content'];
        }

        return false;
    }
}
