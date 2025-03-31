<?php

namespace Boctulus\Simplerest\Modules\Robot;

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