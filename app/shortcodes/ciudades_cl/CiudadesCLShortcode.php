<?php

namespace simplerest\shortcodes\ciudades_cl;

/*
    Especificamente en Wordpress se corrompe y se queda en "Searching..." muchas veces

    Es un tipo de interferencia que NO tiene relacion directa con el plugin 
    "states-cities-and-places-for-woocommerce"
*/

class CiudadesCLShortcode
{
    function __construct(){
        // Evitar incluir jQuery aqui o podria incluirse dos veces

        js_file ('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js');    
        css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css');

        css_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css');
        js_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js');

        $this->index();
    }

    function index()
    {  
        global $places;

        require_once ABSPATH . PLUGINDIR . '/states-cities-and-places-for-woocommerce/places/CL.php';

        foreach ($places['CL'] as $state => $cities) {
            $stateArray = array('state' => $state, 'cities' => $cities);
            $data['states'][] = $stateArray;
        }  
        
        $json = json_encode($data);

        view(__DIR__ . '/views/bt5_modal_variant.php', [
            'json' =>  $json
        ]);              
    }
    
}