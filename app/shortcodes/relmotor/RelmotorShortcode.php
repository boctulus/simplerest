<?php

namespace simplerest\shortcodes\relmotor;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;
use simplerest\core\libs\Paginator;

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


        js_file(JS_PATH .'transients.js');

        js_file(__DIR__ . '/assets/js/search-engine.js');

        $this->index();
    }

    function index()
    {
        css_file(__DIR__ . '/assets/css/styles.css');
        css_file(__DIR__ . '/assets/css/results.css');     

        view(__DIR__ . '/views/relmotor.php');        
    }
}