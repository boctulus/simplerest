<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Libs\HaulmerSignatureSDK;
use Boctulus\Simplerest\Core\Controllers\Controller;

/*
    Flujo:

    - Se genera un TOKEN para obtener una firma con generateToken()
    - Se envian los datos del customer a firmar con createSignature()
    - Puedo determinar el estado del proceso de la firma con getSignatureDetails()

    Entiendo que...

    - Se puede saber si dado el credito que tengo con Haulmer me quedan aun tokens por generar o no con getRemainingSignatures()
    - Puedo saber cuantos tokens he generado (o usado?) con getUsedSignatures()
*/
class SignatureTestController extends Controller
{
    private $sdk;

    public function __construct() {
        parent::__construct();

        $sandbox = false; //
        $this->sdk = new HaulmerSignatureSDK('b61b4586095f460994fa0582785abab2', $sandbox);

        if ($sandbox){
            $this->sdk->setCache(48 * 3600);
            $this->sdk->getClient()->enablePostRequestCache();
        }        
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
        La primera peticion con los mismos datos entrega:

        {"result":"success","message":"","email":"correo.pruebas.qa7@gmail.com","signature":"MIIPEQIBAzC...."}

        pero si se repite:

        {"result":"success","message":"Process is complete","email":"correo.pruebas.qa7@gmail.com"}
    */
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
            // dd($cli->getStatus(), 'STATUS');
            // dd($cli->getError(), 'ERROR');
            dd($cli->data(), 'DATA');

            return $result;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /*
        {"partner_name":"javierPrueba","partner_email":"javier.aliaga@haulmer.com","remaining":17}
    */
    public function get_remaining() {
        try {
            $result = $this->sdk->getRemainingSignatures();
            return $result;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /*
        {"partner_name":"javierPrueba","partner_email":"javier.aliaga@haulmer.com","usages":364}
    */
    public function get_used() {
        try {
            $result = $this->sdk->getUsedSignatures();
            return $result;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /*  
        {"status":"setup complete","client_name":"RICARDO ANDR\u00c9S AHUMADA LEIVA","client_rut":"18280886-5","client_email":"correo.pruebas.qa7@gmail.com"}

        Los status posibles son "waiting user data" y "setup completed"
    */
    public function get_signature_info() {
        try {
            $token = "ed5c5eef-869f-4484-a84c-ce4ba39f622a";

            $result = $this->sdk->getSignatureDetails($token);            
            return $result;
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}