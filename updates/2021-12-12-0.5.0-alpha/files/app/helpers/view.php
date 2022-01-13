<?php

use simplerest\views\MyView; //
use simplerest\core\libs\Strings;

function view(string $view_path, array $vars_to_be_passed  = null, string $layout = 'app_layout.php', string $footer = null){
    return (new MyView($view_path, $vars_to_be_passed, $layout, $footer)); 
}

function assets($resource){
    $base  = config()['BASE_URL'];
 
    if (Strings::endsWith('/', $base)){
        $base = substr($base, 0, -1); 
    }
        
    $public =  $base /* . '/public' */ ;
    return http_protocol() . '://' . $_SERVER['HTTP_HOST']. $public. '/assets/'.$resource;
}

function section($view, Array $variables = []){
    global $ctrl;

    if (!empty($variables)){
        extract($variables);
    }

    include VIEWS_PATH . $view;
}