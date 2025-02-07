<?php

namespace simplerest\libs;

use Boctulus\ApiClient\ApiClientFallback;
use simplerest\core\libs\Strings;

class HaulmerSignatureSDK {
    private $apiClient;
    private $apiKey;
    private $base_url = 'https://api.haulmer.com/v2.0';

    public function __construct($apiKey, $sandbox = false) {
        $this->apiKey = $apiKey;
        $this->apiClient = new ApiClientFallback();
        
        if($sandbox) {
            $this->base_url = 'https://api.haulmer.dev/v2.0';
        }
    }

    public function getClient(){
        return $this->apiClient
        ->setHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json'
        ]);
    }

    public function generateToken($period, $email) {
        if (!is_int($period) || $period < 1 || $period > 3) {
            throw new \Exception("Period must be between 1 and 3 years");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }

        return $this->apiClient                        
            ->setBody([
                'period' => $period,
                'email' => $email
            ], true)   
            ->request($this->base_url.'/partners/signature/generate', 'POST')         
            ->data();
    }

    public function createSignature($token, $data) {
        // Validaciones
        $required = ['names', 'f_lastname', 'rut', 'serie_ci', 'email', 'password'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("Missing required field: $field");
            }
        }

        // Validaciones específicas
        if (strlen($data['names']) > 20 || strlen($data['names']) < 1) {
            throw new \Exception("Names must be between 1 and 20 characters");
        }

        if (strlen($data['password']) < 10 || strlen($data['password']) > 32) {
            throw new \Exception("Password must be between 10 and 32 characters");
        }

        return $this->apiClient
            ->setUrl($this->base_url."/partners/signature/createSignature/$token")
            ->setBody($data, true)
            ->post()
            ->data();
    }

    public function getRemainingSignatures() {
        return $this->apiClient
            ->setUrl($this->base_url.'/partners/signature/remaining')            
            ->get()
            ->data();
    }

    public function getUsedSignatures() {
        return $this->apiClient
            ->setUrl($this->base_url.'/partners/signature/usages')            
            ->get()
            ->data();
    }

    public function getSetupInfo($token) {
        return $this->apiClient
            ->setUrl($this->base_url."/partners/signature/details/$token")            
            ->get()
            ->data();
    }
}