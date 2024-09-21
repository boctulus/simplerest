<?php

namespace simplerest\shortcodes\relmotor;

class RelmotorShortcode
{
    function __construct(){
        // No incluir jQuery aqui o podria incluirse dos veces

        # FontAwesome 5
        js_file('third_party/fontawesome/5/fontawesome_kit.js');
        
        # Select 2
        css_file(__DIR__ . '/assets/third_party/select2/select2.min.css');
        css_file(__DIR__ . '/assets/third_party/select2/select2-bootstrap-5-theme.min.css'); 
        js_file(__DIR__ . '/assets/third_party/select2/select2.min.js');

        // CACHE con local y session Storage
        js_file(JS_PATH .'transients.js');

        js_file(__DIR__ . '/assets/js/search-engine.js');
        js_file(__DIR__ . '/assets/js/populte-dropdowns.js');        

        $this->index();
    }

    function index()
    {
        css_file(__DIR__ . '/assets/css/styles.css');
        css_file(__DIR__ . '/assets/css/results.css');     

        /*
            Atributos que se quieren incluir en los filtros (no tienen porque ser todos)
        */
        $atts = [
            "Sistema Eléctrico",
            "Marca"
        ];

        $user_id = 60; // <--- en WordPress es obtenido 

        view(__DIR__ . '/views/relmotor.php', [
            'atts'    => $atts,
            'user_id' => $user_id  // usuario logueado
        ]);        
    }
}