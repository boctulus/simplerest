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

    public function create_signature() {
        try {
            $token = "ed5c5eef-869f-4484-a84c-ce4ba39f622a"; // token generado
            $result = $this->sdk->createSignature($token, [
               "names" => "Ricardo Andrés",
                "f_lastname"  => "Ahumada",
                "m_lastname"  => "Leiva",
                "rut"  => "18280886-5",
                "serie_ci"  => "B52947255",
                "email"  => "correo.pruebas.qa7@gmail.com",
                "password"  => "prueba123456"
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