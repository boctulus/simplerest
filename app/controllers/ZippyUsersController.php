<?php

namespace Boctulus\Simplerest\controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class ZippyUsersController extends Controller
{
    protected $firebaseApiKey;

    function __construct() { 
        parent::__construct(); 

        // https://console.firebase.google.com/project/zippycart-66e25/settings/general/web:MmRjNzRiNmItZDYzZi00MzU4LWE5NzMtNTY2ZGE5NzVhOGJl
        $credentials_path = ETC_PATH . 'zippycart_credentials.json';
        
        if (!file_exists($credentials_path)) {
            dd("No existe el archivo de credenciales de Firebase en la ruta: $credentials_path");
        }

        if (!is_readable($credentials_path)) {
            dd("No se puede leer el archivo de credenciales de Firebase en la ruta: $credentials_path");
        }        

        $credentials = json_decode(file_get_contents($credentials_path), true);
        
        if ($credentials === null) {
            dd("Error al decodificar el archivo de credenciales de Firebase: " . json_last_error_msg());
        }

        $this->firebaseApiKey = $credentials["apiKey"] ?? die("No se encontró la clave API de Firebase en el archivo de credenciales.");    
    }

    function index()
    {
        $this->login();
    }

    // Función para autenticar usuario con email y contraseña usando la API REST de Firebase
    function _login($email, $password) {
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . $this->firebaseApiKey;
        $data = [
            "email" => $email,
            "password" => $password,
            "returnSecureToken" => true
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            return json_decode($response, true);
        } else {
            return ["error" => ["message" => "Request failed with HTTP code $httpCode"]];
        }
    }

    function login(){
        view('userlogin/login.php');
    }

   
}

