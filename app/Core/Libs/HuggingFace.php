<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

/*
    Sin ensayar
*/
class HuggingFace
{
    const DEFAULT_API_VERSION = 'v1';
    const API_BASE_URL = 'https://api.huggingface.co/models';

    protected $api_key;
    protected $api_version;
    protected $inputs = [];
    protected $response;
    protected $params;
    public $client;

    private $model_endpoints = [
        'distilbert-base-uncased' => '/distilbert-base-uncased',
        'roberta-base'            => '/roberta-base',
        'gpt-neo-2.7B'            => '/gpt-neo-2.7B'
    ];

    function __construct($api_key = null, $api_version = null) {
        $this->api_key = $api_key ?? Config::get()['huggingface_api_key'] ?? die('huggingface_api_key is required');
        $this->api_version = $api_version ?? self::DEFAULT_API_VERSION;
        $this->client = ApiClient::instance()
            ->setHeaders([
                "Content-type" => "application/json",
                "Authorization" => "Bearer " . $this->api_key
            ])
            ->disableSSL()
            ->enablePostRequestCache()
            ->decode();
    }

    function addInput($input) {
        $this->inputs[] = $input;
    }

    function setParams(array $arr) {
        $this->params = $arr;
    }

    function exec($model = 'distilbert-base-uncased')
    {
        if (!array_key_exists($model, $this->model_endpoints)) {
            throw new \Exception("Model not found for '$model'");
        }
        return $this->exec_model($model);
    }

    private function exec_model($model)
    {
        $endpoint = self::API_BASE_URL . $this->model_endpoints[$model];

        $data = [
            'inputs' => $this->inputs
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

        if (isset($this->response['data']['error'])) {
            $this->response['error'] = $this->response['data']['error'];
            unset($this->response['data']['error']);
        }

        return $this->response;
    }
}
