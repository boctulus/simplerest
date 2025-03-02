<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\AIChat;

class GrokAI implements AIChat 
{
    protected $api_key;    
    protected $model = 'grok-beta';
    protected $messages = [];
    protected $params;
    protected $response;
    protected $error_msg;
    public $client;

    function __construct($api_key = null) {
        $this->api_key = $api_key ?? Config::get()['xai_api_key'] ?? die('xai_api_key is required');

        $this->client = ApiClient::instance()
        ->setHeaders([
            "Content-Type"  => "application/json",
            "Authorization" => "Bearer {$this->api_key}"
        ])
        ->disableSSL()
        ->enablePostRequestCache()
        ->decode();
    }

    function getClient(){
        return $this->client;
    }

    function setModel($name){
        $this->model = $name;
        return $this;
    }

    function getModel(){
        return $this->model;
    }

    function setTemperature($val = 0){
        $this->params['temperature'] = $val;
        return $this;
    }

    function addContent($content, $role = 'user'){
        $this->messages[] = [
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
        if ($model === null){
            $model = $this->model;
        }

        $endpoint = 'https://api.x.ai/v1/chat/completions';
    
        $data = [
            'model'    => $model,
            'messages' => $this->messages,
            'stream'   => false
        ];

        if (!empty($this->params)){
            $data = array_merge($data, $this->params);
        }        
    
        $this->client
        ->setBody($data)
        ->post($endpoint);

        $this->response = $this->client->getResponse();

        if ($this->response['error']) {
            $this->error_msg = $this->response['error'];
            return false;
        }

        if ($this->response['http_code'] !== 200) {
            $this->error_msg = "HTTP error: " . $this->response['http_code'];
            return false;
        }

        return $this->response;
    }

    function getFinishReason(){
        return $this->response['data']['choices'][0]['finish_reason'] ?? null;
    }

    function getTokenUsage(){        
        return $this->response['data']['usage'] ?? null;
    }

    function isComplete(){
        return ($this->getFinishReason() === 'stop');
    }

    function getContent($decode = true){
        if (!empty($this->error_msg)) {
            return false;
        }

        if (!isset($this->response['data']['choices'][0]['message']['content'])) {
            return null;
        }

        $content = $this->response['data']['choices'][0]['message']['content'];

        if ($decode){
            if (preg_match('/```json\s*(.+?)\s*```/s', $content, $matches)) {
                $json_string = $matches[1];
                try {
                    $decoded = json_decode($json_string, true);
                    if ($decoded !== null) {
                        return $decoded;
                    }
                } catch (\Exception $e) {}
            }
        }

        return $content;
    }

    // Métodos adicionales que podrían ser útiles
    
    function getMessageRole() {
        return $this->response['data']['choices'][0]['message']['role'] ?? null;
    }

    function getRefusal() {
        return $this->response['data']['choices'][0]['message']['refusal'] ?? null;
    }

    function getModelName() {
        return $this->response['data']['model'] ?? null;
    }

    function getResponseId() {
        return $this->response['data']['id'] ?? null;
    }

    function getSystemFingerprint() {
        return $this->response['data']['system_fingerprint'] ?? null;
    }

    function getPromptTokensDetails() {
        return $this->response['data']['usage']['prompt_tokens_details'] ?? null;
    }
}
