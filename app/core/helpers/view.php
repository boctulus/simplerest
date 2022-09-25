<?php

use simplerest\core\View;
use simplerest\views\MyView; 
use simplerest\core\libs\Config;
use simplerest\core\libs\Strings;

function meta($name, $content){
    return "<meta name=\"$name\" content=\"$content\">\r\n";
}

function link_css($css_file){
    return "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css_file\">\r\n";
}

function js_inline($js_file){
    return "<script type=\"text/javascript\" src=\"$js_file\"></script>\r\n";
}

function view(string $view_path, ?array $vars_to_be_passed  = null, ?string $layout = null, int $expiration_time = 0){
    if (!Strings::endsWith('.php', $view_path)){
        $view_path .= '.php';
    }

    return (new MyView($view_path, $vars_to_be_passed, $layout, $expiration_time)); 
}

/*
    Renderiza el template

    A diferencia de view(), no requiere de una vista
*/
function render($content = null, ?string $layout = null){
    $config = config();

    if (empty($layout)){
        $layout = $config['template'];
    }

    $path = VIEWS_PATH . "$layout";

    if (!file_exists($path)){
        response("Path '$path' not found", 404);
    }

    include $path;
}

function set_template(string $file){
    Config::set('template', $file);
}

function asset($resource){
    $protocol = is_cli() ? 'http' : httpProtocol();
    
    $base  = config()['base_url'];
 
    if (Strings::endsWith('/', $base)){
        $base = substr($base, 0, -1); 
    }

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


function render_metas(){
    $head = View::getHead();

    if (!isset($head['meta'])){
        return;
    }

    foreach ($head['meta'] as $m){
        echo meta($m['name'], $m['content']) . "\r\n";
    }
}

function render_js(bool $in_head = false){
    $arr = $in_head ? View::getHead() : View::getFooter();

    if (!isset($arr['js'])){
        return;
    }

    foreach ($arr['js'] as $_js){
        if (isset($_js['file'])){
            if (substr($_js['file'], 0, 4) != 'http'){
                $path = base_url() . $_js['file'];
            } else {
                $path = $_js['file']    ;
            }	
            
            echo js_inline($path);
        } else {
            echo "<script>$_js</script>\r\n";
        }
    }
}

function render_css(){
    $head = View::getHead();

    if (!isset($head['css'])){
        return;
    }
   
    foreach ($head['css'] as $_css){
        if (isset($_css['file'])){
            echo link_css($_css['file']) . "\r\n";
        } else {
            echo "<style>$_css</style>\r\n";
        }
    }
}


function js_file(string $file, ?Array $atts = null, bool $in_head = false){
   return View::js_file($file, $atts, $in_head);
}

function js(string $file, ?Array $atts = null, bool $in_head = false){
    return View::js($file, $atts, $in_head);
}

function css_file(string $file){
    return View::css_file($file);
}

function css(string $file){
    return View::css($file);
}

// depredicar -->

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

function include_widget_css(string $name){
    include_css(WIDGETS_PATH . $name . '/' . $name . '.css');
}