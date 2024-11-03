<?php

namespace simplerest\shortcodes\relmotor;

class RelmotorShortcode
{
    function __construct(){
        // No incluir jQuery aqui o podria incluirse dos veces

        # FontAwesome 6
        js_file('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css');
        css_file('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js');
        
        # Select 2
        css_file(__DIR__ . '/assets/third_party/select2/select2.min.css');
        css_file(__DIR__ . '/assets/third_party/select2/select2-bootstrap-5-theme.min.css'); 
        js_file(__DIR__ . '/assets/third_party/select2/select2.min.js');

        # Toastr

        css_file(ASSETS_PATH. '/third_party/toastr/toastr.min.css');        
        js_file(ASSETS_PATH . '/third_party/toastr/toastr.min.js');

        // CACHE con local y session Storage
        js_file(JS_PATH .'transients.js');

        // Paginacion
        js_file(JS_PATH .'paginator.js');  
        js_file(JS_PATH .'bootstrap_paginator.js'); 

        js_file(__DIR__ . '/assets/js/search-engine.js');
        js_file(__DIR__ . '/assets/js/populte-dropdowns.js');   
        js_file(__DIR__ . '/assets/js/search-form.js'); 
        
        
        // Printing
        // js_file("third_party/printThis/printThis.js");


        $this->index();
    }

    function index()
    {
        css_file(__DIR__ . '/assets/css/search-form.css');
        css_file(__DIR__ . '/assets/css/results.css');     
        css_file(__DIR__ . '/assets/css/quick_view.css');
        css_file(__DIR__ . '/assets/css/customization.css');

        /*
            Atributos que se quieren incluir en los filtros (no tienen porque ser todos)
        */
        $atts = [
            [
                "key"  => "pa_sistema-electrico",
                "name" => "Sistema Eléctrico"
            ],
            [
                "key"  => "pa_marca",
                "name" => "Marca"
            ],            
        ];

        $user_id = 60; // <--- en WordPress es obtenido 

        view(__DIR__ . '/views/search-form.php', [
            'atts'    => $atts,
            'user_id' => $user_id  // usuario logueado
        ]);        
    }
}