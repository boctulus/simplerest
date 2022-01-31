<?php

namespace simplerest\views;

use simplerest\core\View;

class MyView extends View 
{
    function __construct(string $view_path, array $vars_to_be_passed  = null, ?string $layout = null, int $expiration_time = 0)
    {
        /*
            Added (as example)
        */
        
        $vars_to_be_passed['my_var'] = 'Hello World!';

        parent::__construct($view_path, $vars_to_be_passed, $layout, $expiration_time);
    }
}