<?php

use simplerest\core\View;
use simplerest\libs\Factory;

function view(string $view_path, array $vars_to_be_passed  = null, string $layout = 'app_layout.php', string $footer = null){
    return (new View($view_path, $vars_to_be_passed, $layout, $footer)); 
}

function assets($resource){       
    $public =  config()['BASE_URL'] . '/public';
    return http_protocol() . '://' . $_SERVER['HTTP_HOST']. $public. '/assets/'.$resource;
}

function section($view){
    include VIEWS_PATH . $view;
}