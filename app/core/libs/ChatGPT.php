<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\AIChat;


/*
    En caso de error se recupera con el metodo error()
*/

class ChatGPT implements AIChat
{
    protected $api_key;    
    protected $model = 'gpt-4o-mini';
    protected $messages = [];
    protected $params;
    protected $response;
    protected $error_msg;
    protected $dynamic_token_usage = false;

    /*
        La idea es limitar la cantidad de "mensajes" enviados en caso de que se sobrepase el limite de tokens

        Para COMPLETIONS es $this->messages mientras que para CHAT_COMPLETIONS es $this->messages[0]['content'] 
    */
    protected $dynamic_res_lenght  = false;

    // API client
    public $client;

    const COMPLETIONS = 1;
    const CHAT_COMPLETIONS = 2;
    const MODERATION = 10;
    const IMAGES = 20;
    const AUDIO  = 30;
    const EMBEDDINGS  = 40;

    function __construct($api_key = null) {
        $this->api_key = $api_key ?? Config::get()['openai_api_key'] ?? die('openai_api_key is required');

        $this->client = ApiClient::instance()
        ->setHeaders(
            [
                "Content-type"  => "application/json",
                "Authorization" => "Bearer {$this->api_key}"
            ]
        )
        ->disableSSL()
        ->enablePostRequestCache()
        ->decode();
    }

    // Retorna instancia de API client (habilita poner la cache a funcionar, etc)
    function getClient(){
        return $this->client;
    }

    // podria verificar el modelo este soportado via in_array()
    function setModel($name){
        $this->model = $name;
        return $this;
    }

    function getModel(){
        return $this->model;
    }
    
    function setMaxTokens(int $val){
        $this->params['max_tokens'] = $val;
        return $this;
    }

    function getMaxTokens(){
        return $this->params['max_tokens'] ?? null;
    }

    function setTemperature($val = 0.5){
        $this->params['temperature'] = $val;
        return $this;
    }

    function dynamicTokenUsage(){
        $this->dynamic_token_usage = true;
        return $this;
    }

    function dynamicResponseLenght(){
        $this->dynamic_res_lenght = true;
        return $this;
    }

    function addContent($content, $role = 'user'){
        $this->messages[] = 
        [
            'role'    => $role, 
            'content' => $content
        ];
    }

    function setParams(Array $arr){
        $this->params = $arr;
        return $this;
    }

    function error(){
        return $this->error_msg;
    }

    function exec($model = null)
    {
        $model_endpoints = [   
            // Modelos O1         
            'o1-preview'               => static::CHAT_COMPLETIONS, // CHAT_COMPLETIONS ??  -- $ algo caro
            'o1-preview-2024-09-12'    => static::CHAT_COMPLETIONS, // CHAT_COMPLETIONS ??
            'o1-mini'                  => static::CHAT_COMPLETIONS, // CHAT_COMPLETIONS ??  -- $ accesible
            'o1-mini-2024-09-12'       => static::CHAT_COMPLETIONS, // CHAT_COMPLETIONS ??

            // Modelos GPT-4
            'gpt-4o-mini'              => static::CHAT_COMPLETIONS, // Model with vision capabilities
            'gpt-4o'                   => static::CHAT_COMPLETIONS, // Model with vision capabilities (cheaper than 'gpt-4-vision-preview')
            'gpt-4o-2024-08-06'        => static::CHAT_COMPLETIONS, // Model with vision capabilities
            'gpt-4'                    => static::CHAT_COMPLETIONS, // ok
            'gpt-4-1106-preview'       => static::COMPLETIONS,
            'gpt-4-0613'               => static::COMPLETIONS,
            'gpt-4-32k'                => static::COMPLETIONS,
            'gpt-4-32k-0613'           => static::COMPLETIONS,
        
            // Modelos GPT-3.5  <------ ahora son mas caros que 'gpt-4o-mini'  !!!!! 
            'gpt-3.5-turbo-1106'       => static::CHAT_COMPLETIONS, // ok
            'gpt-3.5-turbo'            => static::COMPLETIONS, // Check compatibility with CHAT_COMPLETIONS
            'gpt-3.5-turbo-16k'        => static::COMPLETIONS,
            'gpt-3.5-turbo-instruct'   => static::COMPLETIONS,
        
            // Modelos DALL·E
            'dall-e-3'                 => static::IMAGES, // Image-specific endpoint
            'dall-e-2'                 => static::IMAGES,
        
            // Modelos TTS (Text-to-Speech)
            'tts-1'                    => static::AUDIO, // Audio-specific endpoint
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
            // ... otros modelos según la documentación de ChatGPT
        ];

        if ($model === null){
            $model = $this->model;
        }

        if (!isset($model_endpoints[$model])){
            throw new \InvalidArgumentException("Model not supported");
        }

        if (!$model_endpoints[$model] == static::COMPLETIONS){
            return $this->exec_chat_completion($model);
        }

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

            if ($endpoint == static::IMAGES){
                return $this->exec_image_generation($model);
            }
        }
    }

    function exec_chat_completion($model)
    {
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        if ($model === null){
            $model = $this->model;
        }
    
        $data = [
            'model'    => $model, 
            'messages' => $this->messages
        ];

        if (!empty($this->params)){
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

        if (is_string($this->response['data'])){
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        if (isset($this->response['data']['data'])){
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])){
            $this->error_msg         = $this->response['data']['error'];
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

        if (!empty($this->params)){
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

        if (is_string($this->response['data'])){
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        if (isset($this->response['data']['data'])){
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])){
            $this->error_msg         = $this->response['data']['error'];
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }
        
        return $this->response;
    }

    function getTokenUsage(){        
        return $this->response['data']['usage'] ?? null;
    }

    function getFinishReason(){
        return $this->response['data']['choices'][0]['finish_reason'] ?? null;
    }

    function wereTokenEnough(){
        return ($this->getFinishReason() != 'length');
    }

    /*
        stop: La generación se detuvo porque el modelo completó la respuesta de manera natural, alcanzando un punto donde decidió que no era necesario generar más texto.

        length: La generación se detuvo porque se alcanzó el límite máximo de tokens permitidos por el parámetro max_tokens o el límite de tokens total del modelo (ej. 4096 tokens en GPT-4). Es una señal de que la respuesta fue truncada antes de que el modelo completara su generación.

        content_filter: La generación fue detenida debido a una política de filtrado de contenido, lo cual ocurre si el contenido generado es inapropiado según los filtros del modelo.

        null o no presente: Si finish_reason es null o no está presente, podría indicar que hubo un error en la generación o que no se completó la solicitud correctamente.
    */
    function isComplete(){
        return ($this->getFinishReason() == 'stop');
    }

    /*
        Podria extraer cualquier cosa y no solo JSON entre ```json y ```
        sino Javascript entre ```javascript y ```, etc.
    */
    function getContent($decode = true){
        if (!empty($this->error_msg)) {
            return false;
        }

        $content = $this->response['data']['choices'][0]['message']['content'];

        if ($decode){
            if (preg_match('/```json\s*(.+?)\s*```/s', $content, $matches)) {
                // Extraemos el contenido del JSON capturado
                $json_string = $matches[1];
                
                // Decodificamos el JSON para manipularlo como un array o un objeto
                $content = json_decode($json_string, true);
            }
        }

        return $content;
    }

    function exec_image_generation($model = null)
    {
        $endpoint = 'https://api.openai.com/v1/images/generations';

        if ($model === null){
            $model = $this->model;
        }

        $data = [
            'model'  => $model,
            'prompt' => $this->messages[0]['content']
        ];

        if (!empty($this->params)){
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

        if (is_string($this->response['data'])){
            $this->response['data'] = json_decode($this->response['data'], true);
        }

        if (isset($this->response['data']['data'])){
            $this->response['data'] = $this->response['data']['data'];
        }

        if (isset($this->response['data']['error'])){
            $this->error_msg         = $this->response['data']['error'];
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }

        return $this->response;
    }

    /*
        Genera error:

        --| ERROR
        Array
        (
            [message] => Invalid type for 'messages[0].content': expected one of a string or array of objects, but got an object instead.
            [type] => invalid_request_error
            [param] => messages[0].content
            [code] => invalid_type
        )
        
        https://community.openai.com/t/image-url-for-gpt-4o-api-giving-error-expected-an-object-but-got-a-string-instead/748188/2

        Probar con cliente en Python
    */
    function analyzeImage($image_url) {
        // Crear el mensaje con el contenido de texto y la imagen en formato base64
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
    
        // Añadir el mensaje a la instancia de ChatGPT
        foreach ($messages as $message) {
            $this->addContent($message, $message['role']);
        }
    
        // Configurar parámetros adicionales si es necesario (opcional)
        $params = [
            'temperature' => 0.5,
            'max_tokens' => 300
        ];

        $this->setParams($params);
    
        // Ejecutar el análisis de la imagen con el modelo de visión
        $response = $this->exec('gpt-4o');
    
        // Manejar la respuesta
        if ($response['status'] == 200) {
            $analysis = $response['data']['choices'][0]['message']['content'];
            return $analysis;
        } else {
            return false;
        }
    }
    
}
