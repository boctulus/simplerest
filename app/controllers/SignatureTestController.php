<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\libs\HaulmerSignatureSDK;

class SignatureTestController extends Controller
{
    private $sdk;

    public function __construct() {
        parent::__construct();
        $this->sdk = new HaulmerSignatureSDK('cebc90896c0445599e6d2269b9f89c8f', true);
    }

    public function generate_token() {
        try {
            $result = $this->sdk->generateToken(1, "test@example.com");

            // $cli = $this->sdk->getClient();
            // dd($cli->getStatus(), 'STATUS');
            // dd($cli->getError(), 'ERROR');
            // dd($cli->data(), 'DATA');

            return $result;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /*
        Debo enviar algo como:
        
        curl --location 'https://api.haulmer.dev/v2.0/partners/signature/createSignature/7151e745-a704-42c4-ad81-50bd301e07fe' \
        --header 'apikey: cebc90896c0445599e6d2269b9f89c8f' \
        --header 'Content-Type: application/json' \
        --data-raw '{
            "names" : "test",
            "f_lastname" : "test",
            "m_lastname" : "test",
            "rut" : "12345678-3",
            "serie_ci" : "123456789",
            "email": "correo.pruebas.qa7@gmail.com",
            "password": "prueba123456"
        }'
    */
    public function create_signature() {
        try {
            $token = "4953f1d1-313d-476f-921d-00789363e147";
            $result = $this->sdk->createSignature($token, [
                'names' => 'Juan',
                'f_lastname' => 'Pérez',
                'm_lastname' => 'López',
                'rut' => '12345678-9',
                'serie_ci' => '123456789',
                'email' => 'juan@example.com',
                'password' => 'password123456'
            ]);

            $cli = $this->sdk->getClient();
            dd($cli->getStatus(), 'STATUS');
            dd($cli->getError(), 'ERROR');
            dd($cli->data(), 'DATA');

            return $result;
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get_remaining() {
        try {
            $result = $this->sdk->getRemainingSignatures();
            return $result;
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get_used() {
        try {
            $result = $this->sdk->getUsedSignatures();
            return $result;
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get_setup_info() {
        try {
            $token = "token-a-consultar";

            $result = $this->sdk->getSetupInfo($token);            
            return $result;
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}