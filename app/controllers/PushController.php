<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\libs\OneSignal;

class PushController extends MyController
{
    function index(){
        $config = array(
            'app_id' => '9381a718-414c-4f09-b810-2288913de0a0',
            'app_rest_api_key' => 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw',
            'title' => "Un título cualquiera", 
            'body' => 'Esto es el cuerpo del mensaje', 
            'url' => 'http://www.solucionbinaria.com',

            /*  
                Large Images that appear with the notification. Supported by Chrome on Windows, macOS, and Android.
            */
            'image' => 'https://i.imgur.com/Czq0VNR.png',

            'icon' => 'https://i.imgur.com/Czq0VNR.png',
            
            /*
                Solo en Android / Amazon se muestra el ícono en el botón
            */
            'buttons' => [
                [
                    "id" => "like-button-2",
                    "text" => "Like2",
                    "icon" => "http://i.imgur.com/N8SN8ZS.png",
                    "url" => "https://yoursite.com"
                ]
            ],
            'extra' => [
                'campo1' => 'valor1',
                'campo2' => 'valor2'
            ]
        );
        
        $res  = OneSignal::send($config);
        
        $data = json_decode($res, true);

        if (isset($data['errors'])){
            d($data['errors'], 'Errores');
        } else {
            d($data);
        }
    }
}

