<?php

namespace simplerest\core\libs;


class OpenAI
{
    protected $api_key;
    protected $messages;
    protected $response;

    const COMPLETIONS = 1;
    const CHAT_COMPLETIONS = 2;
    const MODERATION = 10;
    const IMAGES = 20;
    const AUDIO  = 30;
    const EMBEDDINGS  = 40;

    function __construct($api_key = null) {
        $this->api_key = $api_key ?? config()['openai_api_key'] ?? die('openai_api_key is required');
    }

    function addContent($content, $role = 'user'){
        $this->messages[] = 
        [
            'role'    => $role, 
            'content' => $content
        ];
    }

    function exec($model = 'gpt-3.5-turbo-1106')
    {
        $model_endpoints = [
            // Modelos GPT-4
            'gpt-4'                    => static::CHAT_COMPLETIONS, // ok
            'gpt-4-1106-preview'       => static::COMPLETIONS,
            'gpt-4-vision-preview'     => static::COMPLETIONS, // Modelo con capacidades de visión
            'gpt-4-0613'               => static::COMPLETIONS,
            'gpt-4-32k'                => static::COMPLETIONS,
            'gpt-4-32k-0613'           => static::COMPLETIONS,
        
            // Modelos GPT-3.5
            'gpt-3.5-turbo-1106'       => static::CHAT_COMPLETIONS, // ok
            'gpt-3.5-turbo'            => static::COMPLETIONS, // Verificar compatibilidad con CHAT_COMPLETIONS
            'gpt-3.5-turbo-16k'        => static::COMPLETIONS,
            'gpt-3.5-turbo-instruct'   => static::COMPLETIONS,
        
            // Modelos DALL·E
            'dall-e-3'                 => static::IMAGES, // Endpoint específico para imágenes
            'dall-e-2'                 => static::IMAGES,
        
            // Modelos TTS (Text-to-Speech)
            'tts-1'                    => static::AUDIO, // Endpoint específico para audio
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
            // ... otros modelos según la documentación de OpenAI
        ];
        

        if (!array_key_exists($model, $model_endpoints)){
            throw new \Exception("ChatGPT model not found for '$model'");
        }    

        foreach ($model_endpoints as $_model => $endpoint){
            if ($_model != $model){
                continue;
            }

            if ($endpoint == static::CHAT_COMPLETIONS){
                return $this->exec_chat_completion($model);
            }

            if ($endpoint == static::COMPLETIONS){
                return $this->exec_completion($model);
            }
        }
    }

    
    // gpt-3.5-turbo-1106, gpt-4 y otros
    function exec_chat_completion($model)
    {
        $endpoint = 'https://api.openai.com/v1/chat/completions';
    
        $data = [
            'model'    => $model, 
            'messages' => $this->messages
        ];
   
        $cli = ApiClient::instance()
        ->decode();
    
        $cli
        ->setHeaders(
            [
                "Content-type"  => "application/json",
                "Authorization" => "Bearer {$this->api_key}"
            ]
        )
        ->setBody($data)
        ->disableSSL()
        ->post($endpoint);

        $this->response = [
            'status' => $cli->getStatus(),
            'error'  => $cli->getError(),
            'data'   => $cli->getResponse()
        ];

        if (is_string($this->response['data'])){
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        if (isset($this->response['data']['data'])){
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])){
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }
    
        return $this->response;
    }

    function exec_completion($model)
    {
        $endpoint = "https://api.openai.com/v1/engines/$model/completions";
    
        $data = [
            'prompt' => $this->messages[0]['content']
        ];
   
        $cli = ApiClient::instance();
    
        $cli
        ->setHeaders(
            [
                "Content-type"  => "application/json",
                "Authorization" => "Bearer {$this->api_key}"
            ]
        )
        ->setBody($data)
        ->disableSSL()
        ->post($endpoint);

        $this->response = [
            'status' => $cli->getStatus(),
            'error'  => $cli->getError(),
            'data'   => $cli->getResponse()
        ];

        if (is_string($this->response['data'])){
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        if (isset($this->response['data']['data'])){
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])){
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }
        
        return $this->response;
    }

}

