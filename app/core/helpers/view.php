<?php

use simplerest\core\View;
use simplerest\core\libs\Url;
use simplerest\core\libs\Files;
use simplerest\core\libs\Config;
use simplerest\core\libs\Strings;

/*
    Una diferencia importante entre js_file() / css_file() con las de SW
    es que en SR siempre son relativas a VIEW_PATH !!!

    Ej: --en un shortcode--

    // En SR
    css_file('third_party/bootstrap/3.x/normalize.css');  // relativa a VIEW_PATH
    css_file(__DIR__ . '/assets/css/racks.css'); 
    css_file(__DIR__ . '/assets/css/styles.css');

    equivaldria a:

    // En SW
    css_file(Constants::VIEW_PATH . 'third_party/bootstrap/3.x/normalize.css');  // debi agregar Constants::VIEW_PATH
    css_file(__DIR__ . '/assets/css/racks.css');   // podria remover el __DIR__
    css_file(__DIR__ . '/assets/css/styles.css');  // podria remover el __DIR__

    Por tanto la forma "portable" (compatible) es usar siempre rutas absolutas:
    
    css_file(Constants::VIEW_PATH . 'third_party/bootstrap/3.x/normalize.css');  
    css_file(__DIR__ . '/assets/css/racks.css');  
    css_file(__DIR__ . '/assets/css/styles.css'); 
*/

function get_meta_tag(string $name, string $content){
    return "<meta name=\"$name\" content=\"$content\">\r\n";
}

function get_js_tag($js_file){
    return "<script type=\"text/javascript\" src=\"$js_file\"></script>\r\n";
}

function get_css_tag($css_file){
    return "<link href=\"$css_file\" rel=\"stylesheet\" />\r\n";
}

function base(){
    $base_url = base_url();

    return "<base href=\"$base_url\">

    <script>
        const base_url  = '$base_url';        
    </script>";
}

function include_no_render(string $path, ?Array $vars = null){
    global $ctrl;

    if (!empty($vars)){
        extract($vars);
    }      
    
    ob_start();
    include $path;
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function get_view_src(string $view_path, int $expiration_time = 0){
    return View::get_view_src($view_path, $expiration_time);
}

function get_view(string $view_path, ?Array $vars = null, int $expiration_time = 0){
    if (empty($vars)){
        return View::get_view($view_path, $expiration_time);
    }

    // Si hay algo que pasar a la vista, no tendria sentido obtener la version cacheada ? y cachearla?

    return include_no_render(View::get_view_src($view_path), $vars);
}

function view(string $view_path, ?array $vars  = null, ?string $layout = null, int $expiration_time = 0){
    (new View($view_path, $vars, $layout, $expiration_time)); 
}

/*
    Renderiza el template

    A diferencia de view(), no requiere de una vista
*/
function render($content = null, ?string $layout = null, ?array $vars  = null){
    $config = Config::get();

    if (empty($layout)){
        $layout = $config['template'];
    }

    $path = VIEWS_PATH . "$layout";

    if (!file_exists($path)){
        response("Path '$path' not found", 404);
    }

    if (!empty($vars)){
        extract($vars);
    }   

    include $path;
}

function set_template(string $file){
    Config::set('template', $file);
}

/*
    Para ser usado dentro de un shortcode

    Ej:

    <img src="<?= shortcode_asset(__DIR__ . '/images/WES-Logo.png') ?>" />
*/
function shortcode_asset($resource)
{   
    $resource = Files::normalize($resource, '/');
    $resource = Strings::since($resource, '/app/shortcodes/'); 
    
    $url = Url::getBaseUrl() . str_replace('/views/', '/assets/', $resource);
    
    return $url;    
}

/*
    Incluye assets

    Siempre de /public/assets
*/
function asset($resource)
{   
    $resource = Files::normalize($resource, '/');
    $resource = 'public/assets/' . trim($resource, '/');

    $url      = Url::getBaseUrl() . '/';   
    $url      = $url . (!$resource === null ? '' : $resource);

    return $url;
}

function section($view, Array $vars = []){
    global $ctrl;

    if (!empty($vars)){
        extract($vars);
    }

    include VIEWS_PATH . $view;
}

function get_metas(){
    $head = View::getHead();

    if (!isset($head['meta'])){
        return;
    }

    $ret = '';
    foreach ($head['meta'] as $m){
        $ret .= get_meta_tag($m['name'], $m['content']) . "\r\n";
    }

    return $ret;
}

function get_js(bool $in_head = false){
    $arr = $in_head ? View::getHead() : View::getFooter();

    if (!isset($arr['js'])){
        return;
    }

    $ret = '';
    foreach ($arr['js'] as $_js){
        if (isset($_js['file'])){
            $path = $_js['file'];
            
            $ret .= get_js_tag($path);
        } else {
            if (!is_string($_js)){
                throw new \Exception("Expected string. Got " . gettype($_js));
            }

            $ret .= "<script>$_js</script>\r\n";
        }
    }

    return $ret;
}

function get_css(){
    $head = View::getHead();

    if (!isset($head['css'])){
        return;
    }
   
    $ret = '';
    foreach ($head['css'] as $_css){
        if (is_array($_css)){
            if (isset($_css['file'])){
                $ret .= get_css_tag($_css['file']) . "\r\n";
            }            
        } else {
            if (!is_string($_css)){
                throw new \Exception("Expected string. Got " . gettype($_css));
            }

            $ret .= "<style>". $_css . "</style>\r\n";
        }
    }

    return $ret;
}

function head(){
    return 
    get_metas() .
    get_css() .     
    get_js(VIEW::HEAD);
}

function footer(){
    return get_js(VIEW::FOOTER);
}

function js_file(string $file, ?Array $atts = null, bool $in_head = false){
   return View::js_file($file, $atts, $in_head);
}

function js(string $code, ?Array $atts = null, bool $in_head = false){
    return View::js($code, $atts, $in_head);
}

function css_file(string $file){
    return View::css_file($file);
}

function css(string $file){
    return View::css($file);
}

/*
    Antes llamada encodeProp()

    Otra posibilidad seria colocar las variables en un archivo .js que sea el primero en cargarse de los .js
    En este caso deberia ser un solo archivo .js independientemente de cuantas veces se llame a push_var()

    La funcion podira llamarse push_var() y no haria falta nada del lado de JS
*/
function var_encode($name, $value){
    $encoded = base64_encode(is_array($value) ? '--array--' . json_encode($value) : $value);

    echo "<input type=\"hidden\" name=\"$name-encoded\" id=\"$name-encoded\" value=\"$encoded\">";
}

function umodel(){
    $model = get_user_model_name();    
           
    $__email    = $model::$email    ?? '';
    $__username = $model::$username ?? '';
    $__password = $model::$password ?? '';

    return "
    <script>
        let \$__email    = '$__email'; 
        let \$__username = '$__username';
        let \$__password = '$__password';
    </script>";
}

