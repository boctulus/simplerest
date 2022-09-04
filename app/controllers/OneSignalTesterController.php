<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\libs\OneSignal;

class OneSignalTesterController extends MyController
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

            [
                'app_name' => 'Radio Alternativo',
                'app_id'   => '5c8d34f1-14e0-44c5-ab3f-d0620ec7e252',
                'api_key'  => 'ODQ4OTE3MDAtZjgyMC00M2Y3LTg0ODUtNDg5YzNlM2Y0YWEw'
            ],
		];

        $app = 2;

        $config = array(
            'app_id' => $apps[$app]['app_id'],
            'app_rest_api_key' => $apps[$app]['api_key'],
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

    function add_device(int $device_type){
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
            'device_type' => $device_type,
            'segments' => ['All']
        );

        $res  = OneSignal::addDevice($config);

        if (isset($res['errors'])){
            d($res['errors'], 'Errores');
        } else {
            d($res);
        }
    }

    function osig_app(){
        $apps = [
            [
                'app_name' => 'Woo1',
                'app_id'   => '9c5b7327-7279-4032-9f14-2a4d4ca1332c',
                'api_key'  => 'MGVkYjlhY2ItNTgwOS00N2JkLWEwMDQtZTFiOTYzY2NkZDRh'
            ],

            // más apps
            // Ej:

            [
                'app_name' => 'Radio Alternativo',
                'app_id'   => '5c8d34f1-14e0-44c5-ab3f-d0620ec7e252',
                'api_key'  => 'ODQ4OTE3MDAtZjgyMC00M2Y3LTg0ODUtNDg5YzNlM2Y0YWEw'
            ],

            [
                'app_name' => 'SimpleRest',
                'app_id'   => '9381a718-414c-4f09-b810-2288913de0a0',
                'api_key'  => 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw'
            ],
        ];

        $app = 1;

        $config = array(
            'app_id' => $apps[$app]['app_id'],
            'app_rest_api_key' => $apps[$app]['api_key']
        );

        $res  = OneSignal::app($config);

        if (isset($res['errors'])){
            d($res['errors'], 'Errores');
        } else {
            d($res);
        }
    }


    function osig_users(){
        $apps = [
            [
                'app_name' => 'Woo1',
                'app_id'   => '9c5b7327-7279-4032-9f14-2a4d4ca1332c',
                'api_key'  => 'MGVkYjlhY2ItNTgwOS00N2JkLWEwMDQtZTFiOTYzY2NkZDRh'
            ],

            // más apps
            // Ej:

            [
                'app_name' => 'Radio Alternativo',
                'app_id'   => '5c8d34f1-14e0-44c5-ab3f-d0620ec7e252',
                'api_key'  => 'ODQ4OTE3MDAtZjgyMC00M2Y3LTg0ODUtNDg5YzNlM2Y0YWEw'
            ],

            [
                'app_name' => 'SimpleRest',
                'app_id'   => '9381a718-414c-4f09-b810-2288913de0a0',
                'api_key'  => 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw'
            ],
        ];

        $app = 1;

        $config = array(
            'app_id' => $apps[$app]['app_id'],
            'app_rest_api_key' => $apps[$app]['api_key']
        );

        $res  = OneSignal::getUsers($config);

        if (isset($res['errors'])){
            d($res['errors'], 'Errores');
        } else {
            d($res);
        }
    }


    function osig_nots(){
        $apps = [
            [
                'app_name' => 'Woo1',
                'app_id'   => '9c5b7327-7279-4032-9f14-2a4d4ca1332c',
                'api_key'  => 'MGVkYjlhY2ItNTgwOS00N2JkLWEwMDQtZTFiOTYzY2NkZDRh'
            ],

            // más apps
            // Ej:

            [
                'app_name' => 'Radio Alternativo',
                'app_id'   => '5c8d34f1-14e0-44c5-ab3f-d0620ec7e252',
                'api_key'  => 'ODQ4OTE3MDAtZjgyMC00M2Y3LTg0ODUtNDg5YzNlM2Y0YWEw'
            ],

            [
                'app_name' => 'SimpleRest',
                'app_id'   => '9381a718-414c-4f09-b810-2288913de0a0',
                'api_key'  => 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw'
            ],
        ];

        $app = 1;

        $config = array(
            'app_id' => $apps[$app]['app_id'],
            'app_rest_api_key' => $apps[$app]['api_key']
        );

        $res  = OneSignal::getNotifications($config);

        if (isset($res['errors'])){
            d($res['errors'], 'Errores');
        } else {
            d($res);
        }
    }

}

