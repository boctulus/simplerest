<?php

use simplerest\views\MyView; 
use simplerest\core\libs\Strings;

function meta($name, $content){
    return "<meta name=\"$name\" content=\"$content\">";
}

function link_tag($css_file){
    return "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\">";
}

function view(string $view_path, ?array $vars_to_be_passed  = null, ?string $layout = null, int $expiration_time = 0){
    if (!Strings::endsWith('.php', $view_path)){
        $view_path .= '.php';
    }

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


/*
    Para cargar JS dinamicamente desde cualquier vista cheequear y probar:

    https://stackoverflow.com/questions/13121948/dynamically-add-script-tag-with-src-that-may-include-document-write
    https://cleverbeagle.com/blog/articles/tutorial-how-to-load-third-party-scripts-dynamically-in-javascript
    https://stackoverflow.com/a/61996709/980631
*/

// Depredicar de acá hacia abajo -->

function include_css(string $path){
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

function include_js(string $path){
    if (!Strings::endsWith('.js', $path)){
        throw new \InvalidArgumentException("Path '$path' should be to .js file");
    }
    ?>
    <script>
    <?php
        include $path;
    ?>
    </script>
    <?php
}

function css(string $css){
    ?>
    <style>
    <?= $css ?>
    </style>
    <?php
}

function js(string $js){
    ?>
    <script>
    <?= $js ?>
    </script>
    <?php
}

function include_widget_css(string $name){
    include_css(WIDGETS_PATH . $name . '/' . $name . '.css');
}
