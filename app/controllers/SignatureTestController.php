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
            $cli = $this->sdk->getClient();

            dd($cli->getStatus(), 'STATUS');
            dd($cli->getError(), 'ERROR');
            dd($cli->data(), 'DATA');

            return $result;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function create_signature() {
        try {
            $token = "token-obtenido-del-paso-anterior";
            $result = $this->sdk->createSignature($token, [
                'names' => 'Juan',
                'f_lastname' => 'Pérez',
                'm_lastname' => 'López',
                'rut' => '12345678-9',
                'serie_ci' => '123456789',
                'email' => 'juan@example.com',
                'password' => 'password123456'
            ]);

            return $this->response($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get_remaining() {
        try {
            $result = $this->sdk->getRemainingSignatures();
            return $this->response($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get_used() {
        try {
            $result = $this->sdk->getUsedSignatures();
            return $this->response($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get_setup_info() {
        try {
            $token = "token-a-consultar";
            $result = $this->sdk->getSetupInfo($token);
            return $this->response($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}