<?php

namespace simplerest\shortcodes\countdown;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;
use simplerest\core\libs\Paginator;

class CountDownShortcode
{
    function __construct(){
        js_file('third_party/jquery/3.3.1/jquery.min.js'); 
    }

    function counter()
    {
        #css_file(__DIR__ . '/css/styles.css');

        js_file('https://raw.githack.com/epiksel/countdown/master/jquery.countdown.js');
        js_file('https://colaoled.it/js/countdown.js');

        // Deberia poder usarse un identificador que prevenga de cargarlo dos veces
        // como en Wordpress y compatible con WordPress
        css_file('third_party/bootstrap/5.x/bootstrap.min.css');
        js_file('third_party/bootstrap/5.x/bootstrap.bundle.min.js');        
        
        return get_view(__DIR__ . '/views/counter.php');
    }
}