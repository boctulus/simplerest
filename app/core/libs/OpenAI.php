<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class OpenAI
{
    protected $api_key;
    protected $messages;
    protected $response;

    const COMPLETIONS = 1;
    const CHAT_COMPLETIONS = 2;

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
            'gpt-3.5-turbo-1106' => 'chat/completions',
            // ...
        ];

        if (!array_key_exists($model, $model_endpoints)){
            throw new \Exception("ChatGPT model not found for '$model'");
        }    

        foreach ($model_endpoints as $_model => $endpoint){
            if ($_model != $model){
                continue;
            }

            if ($endpoint == 'chat/completions'){
                return $this->exec_chat_completion($model);
            }

            if ($endpoint == '/completions'){
                return $this->exec_completion($model);
            }
        }
    }

    function exec_chat_completion($model)
    {
        $endpoint = 'https://api.openai.com/v1/chat/completions';
    
        $data = [
            'model'    => $model, 
            'messages' => $this->messages
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

        if (isset($this->response['data']['data'])){
            $this->response['data'] = $this->response['data']['data'];
        }
    
        return $this->response;
    }

    // Verificar
    function exec_completion($model)
    {
        $endpoint = "https://api.openai.com/v1/engines/$model/completions";
    
        $data = [
            'messages' => $this->messages
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
    
        return $this->response;
    }

}

