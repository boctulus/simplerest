<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\libs\OneSignal;

class PushController extends MyController
{
    function index()
    {
    	$apps = [
		    [
		        'app_name' => 'Woo1',
		        'app_id'   => '9c5b7327-7279-4032-9f14-2a4d4ca1332c',
		        'api_key'  => 'MGVkYjlhY2ItNTgwOS00N2JkLWEwMDQtZTFiOTYzY2NkZDRh'
		    ],

		    // más apps
		    // Ej:

		    [
		        'app_name' => 'SimpleRest',
		        'app_id'   => '9381a718-414c-4f09-b810-2288913de0a0',
		        'api_key'  => 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw'
		    ],
		];

        $config = array(
            'app_id' => $apps[1]['app_id'],
            'app_rest_api_key' => $apps[1]['api_key'],
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

        if (isset($res['errors'])){
            d($res['errors'], 'Errores');
        } else {
            d($res);
        }
    }
}

