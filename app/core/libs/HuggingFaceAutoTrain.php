<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

/*
    Borrador
*/
class HuggingFaceAutoTrain
{        
    const API_BASE_URL = 'https://api-inference.huggingface.co/autotrain';
    const DEFAULT_API_VERSION = 'v1';

    public    $client;
    protected $params;
    protected $api_key;
    protected $inputs = [];
    protected $response;
    
    function __construct($api_key = null, $api_version = null) {
        $this->api_key = $api_key ?? config()['huggingface_api_key'] ?? die('huggingface_api_key is required');
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


    function createProject($name, $task)
    {
        $endpoint = self::API_BASE_URL . '/projects';
        $data = [
            'name' => $name,
            'task' => $task
        ];
        
        return $this->client->post($endpoint, $data);
    }

    function uploadDataset($projectId, $datasetPath)
    {
        // Implementar lógica para subir dataset
    }

    function startTraining($projectId)
    {
        $endpoint = self::API_BASE_URL . "/projects/{$projectId}/trainings";
        return $this->client->post($endpoint);
    }

    // Más métodos para monitorear entrenamiento, obtener resultados, etc.


}