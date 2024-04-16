<?php

namespace simplerest\shortcodes\eat_leaf;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;

class EatLeaf
{
    function __construct(){
        // css_file('third_party/bulma/bulma.min.css'); 
    }

    function index()
    {
        css_file(__DIR__ . '/assets/css/styles.css');  
        
        return ''
            // . get_view(__DIR__ . '/views/section_1.php') 
            . get_view(__DIR__ . '/views/section_2.php')
            //. get_view(__DIR__ . '/views/section_3-static.php')
            // . get_view(__DIR__ . '/views/section_4.php')
            // . get_view(__DIR__ . '/views/section_5.php')
            // . get_view(__DIR__ . '/views/section_6.php')
        ;
    }
}