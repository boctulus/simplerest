<?php

namespace simplerest\shortcodes\ciudades_cl;

use simplerest\core\libs\DB;

class CiudadesCLShortcode
{
    function __construct(){
        js_file ('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js');    
        css_file('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css');

        css_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css');
        js_file('https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js');

        $this->index();
    }

    function index()
    {  
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