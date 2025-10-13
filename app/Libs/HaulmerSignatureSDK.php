<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\ApiClient\ApiClientFallback;

/*
    @author Pablo Bozzolo

    DOC

    https://doc-api-signature.haulmer.com/
*/
class HaulmerSignatureSDK {
    private $apiClient;
    private $apiKey;
    private $base_url = 'https://api.haulmer.com/v2.0';

    public function __construct($apiKey, $sandbox = false) {
        $this->apiKey = $apiKey;

        $this->apiClient = (new ApiClient())        
        ->setHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json'
        ]);
        
        if($sandbox) {
            $this->base_url = 'https://api.haulmer.dev/v2.0';
        }
    }

    public function setCache($expiration_time){
        $this->apiClient->cache($expiration_time);
    }

    public function getClient(){
        return $this->apiClient;
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
            ->setBody($data, true)
            ->request($this->base_url."/partners/signature/createSignature/$token", 'POST')         
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

    /*
        Dado que la generación de firmas es realizado por los clientes, el integrador no conoce el estado de progreso del proceso. 
        
        Para estar informado se deja a disposición este endpoint que informará el estado y la información básica del cliente 
        (asociado al token de la firma electrónica).

        DESCRIPCIÓN DE RESPUESTA:
        
        status: Indica el estado de progreso del proceso de generación de firma.
        client_name: Nombre del cliente.
        client_rut: RUT del cliente.
        client_email: Email del cliente.

        {
            "status": "setup complete",
            "client_name": "Claudia Munoz Flores",
            "client_rut": "18918017-9",
            "client_email": "correo.pruebas.qa7@gmail.com"
        }

        Si "status" es "setup complete" entonces el cliente ha firmado?

        REQUEST:

        curl --location 'https://api.haulmer.dev/v2.0/partners/signature/details/3abc9f13-6f11-485a-ab4e-6f2485df6a7a' \
        --header 'apiKey: e00d938cb3e5448490a6a1847ce7bf1c'
    */
    public function getSignatureDetails($token) {
        return $this->apiClient
            ->setUrl($this->base_url."/partners/signature/details/$token")            
            ->get()
            ->data();
    }
}