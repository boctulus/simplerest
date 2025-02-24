<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\AIChat;
use simplerest\core\exceptions\NotImplementedException;

/*
    Ej:

    $chat = new ClaudeAI();

    $chat->setParams(['max_tokens' => 200]); 
    $chat->addContent('Estados de oxidacion del Uranio?');
    $res = $chat->exec();
    dd($res);

    y se puede elgir el modelo.

    Ej:

    $chat = new ClaudeAI();

    $chat->setParams(['max_tokens' => 30]);  
    $chat->addContent('Hola, ¿cómo estás hoy?');
    $res = $chat->exec('claude-instant-1.2');
    dd($res);

*/
class ClaudeAI implements AIChat
{
    const DEFAULT_API_VERSION = '2023-06-01';
    
    protected $api_key;
    protected $api_version;
    protected $messages = [];
    protected $response;
    protected $params;

    protected $model = 'claude-3-sonnet-20240229';

    // API client
    public $client;

    const MESSAGES = 1;

    function __construct($api_key = null, $api_version = null) {
        $this->api_key = $api_key ?? config()['claude_api_key'] ?? die('claude_api_key is required');
        $this->api_version = $api_version ?? self::DEFAULT_API_VERSION;

        $this->client = ApiClient::instance()
        ->setHeaders(
            [
                "Content-type" => "application/json",
                "x-api-key" => $this->api_key,
                "anthropic-version" => $this->api_version
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
    }

    function getModel(){
        return $this->model;
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
    }

    function exec($model = null)
    {
        $model_endpoints = [
            'claude-3-opus-20240229'   => static::MESSAGES,
            'claude-3-sonnet-20240229' => static::MESSAGES,
            'claude-3-haiku-20240307'  => static::MESSAGES,
            'claude-2.1'               => static::MESSAGES,
            'claude-2.0'               => static::MESSAGES,
            'claude-instant-1.2'       => static::MESSAGES,
        ];

        if (empty($model)){
            $model = $this->model;
        }

        if (!array_key_exists($model, $model_endpoints)){
            throw new \Exception("Claude AI model not found for '$model'");
        }    

        return $this->exec_messages($model);
    }

    protected function exec_messages($model)
    {
        $endpoint = 'https://api.anthropic.com/v1/messages';
    
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

        if (isset($this->response['data']['content'])){
            $this->response['data'] = $this->response['data']['content'];
        }

        if (isset($this->response['data']['error'])){
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }
    
        return $this->response;
    }

    function exec_chat_completion($model = null){
        throw new NotImplementedException();
    }

    function getTokenUsage(){        
        throw new NotImplementedException();
    }

    function getFinishReason(){
        throw new NotImplementedException();;
    }

    function isComplete(){
        throw new NotImplementedException();
    }

    function getContent($decode = true){
        throw new NotImplementedException();
    }
}
