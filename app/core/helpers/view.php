<?php

use simplerest\views\MyView; 
use simplerest\core\libs\Strings;

function view(string $view_path, array $vars_to_be_passed  = null, ?string $layout = null, int $expiration_time = 0){
    return (new MyView($view_path, $vars_to_be_passed, $layout, $expiration_time)); 
}

function asset($resource){
    $protocol = is_cli() ? 'http' : http_protocol();
    
    $base  = config()['BASE_URL'];
 
    if (Strings::endsWith('/', $base)){
        $base = substr($base, 0, -1); 
    }
        
    # $public =  $base /* . '/public' */ ;
    $public =  $base . '/public';
    return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? env('APP_URL')). $public. '/assets/'.$resource;
}

function section($view, Array $variables = []){
    global $ctrl;

    if (!empty($variables)){
        extract($variables);
    }

    include VIEWS_PATH . $view;
}

function include_style(string $path){
    if (!Strings::endsWith('.css', $path)){
        throw new \InvalidArgumentException("Path '$path' should be to .css file");
    }
    ?>
    <style>
    <?php
        include $path;
    ?>
    </style>
    <?php
}