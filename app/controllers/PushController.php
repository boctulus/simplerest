<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\libs\OneSignal;

class PushController extends MyController
{
    function index(){
        $config = array(
            'app_id' => '9381a718-414c-4f09-b810-2288913de0a0',
            'app_rest_api_key' => 'OWQ3NTRkOWYtZGQ1ZS00ZTkwLThiMjUtNmQ0ODQzNjA2YzMw',
            'title' => "Un título cualquiera", //TITLE
            'body' => 'Esto es el cuerpo del mensaje', //BESKJED
            'url' => 'http://www.solucionbinaria.com', //CONTENT URL
            'image_url' => 'https://solucionbinaria.com/assets/images/servicios/woo2.png',
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
        
        $res  = OneSignal::sendPush($config);
        
        $data = json_decode($res, true);

        if (isset($data['errors'])){
            d($data['errors'], 'Errores');
        } else {
            d($data);
        }
    }
}

