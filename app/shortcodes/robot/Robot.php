<?php

namespace simplerest\shortcodes\robot;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;

class Robot
{
    function __construct(){
        // css_file('third_party/bulma/bulma.min.css'); 
        $this->render();
    }

    function render()
    {
        css_file(__DIR__ . '/assets/css/styles.css');  
        
        view(__DIR__ . '/views/robot.php');
    }
}