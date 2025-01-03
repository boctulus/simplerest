<?php

namespace simplerest\core\libs;

use Sabberworm\CSS\Parser;
use simplerest\core\libs\XML;
use simplerest\core\libs\Strings;
use simplerest\traits\BootstrapTrait;

class CSS
{
    use BootstrapTrait;
    
    /*  
        Dado el HTML de una pagina o el path,

        - Descarga cada archivo .css
        - Generar una linea con css_file() para cada uno

        Con $use_helper en true,

        Salida:

            css_file('practicatest.cl/basic.min.css');
            css_file('practicatest.cl/style.themed.css');
            css_file('practicatest.cl/fontawesome.css');
            css_file('practicatest.cl/brands.css');

        Con $use_helper en false,

        Array
        (
            [0] => D:\www\simplerest\public\assets\practicatest.cl\basic.min.css
            [1] => D:\www\simplerest\public\assets\practicatest.cl\style.themed.css
            [2] => D:\www\simplerest\public\assets\practicatest.cl\fontawesome.css
            [3] => D:\www\simplerest\public\assets\practicatest.cl\brands.css
        )

        Usando $if_callback() se puede filtrar dada una condicion dentro del string

        Ej:

        CSS::downloadAll($url, true, function($url){
            return Strings::contains('/xstore', $url);
        })

        <-- solo descarga si contiene el substring '/xstore'
    */
    static function downloadAll(string $html, bool $use_helper = true, $if_callback = null, $exp_cache = 1800)
    {
        if (Strings::startsWith('https://', $html) || Strings::startsWith('http://', $html)){
            $html = consume_api($html, 'GET', null, null, null, false, false, $exp_cache);
        } else {
            if (strlen($html) <= 255 && Strings::containsAny(['\\', '/'], $html)){
                if (file_exists($html)){
                    $html = Files::getContent($html);
                }            
            }
        }

        $urls = CSS::extractStyleUrls($html, true);

        foreach ($urls as $ix => $url){
            if ($if_callback !== null && is_callable($if_callback) && !$if_callback($url)){
                unset($urls[$ix]);
            }
        }

        // dd($urls, 'URLS');

        $filenames = [];
        foreach ($urls as $url){
            $domain = Url::getDomain($url);
            $path   = ASSETS_PATH . $domain;

            Files::mkDirOrFail($path);        
            $bytes = Files::download($url, $path);   

            if (empty($bytes)){
                throw new \Exception("Download '$url' was not possible");
            }

            $filename    = Files::getFilenameFromURL($url);
            $filenames[] = $path . DIRECTORY_SEPARATOR . $filename;
        }

        if (!$use_helper){
            return $filenames;
        }

        $out = '';
        foreach ($filenames as $ix => $filename){
            $filenames[$ix] = str_replace('\\', '/', Strings::diff($filename, ASSETS_PATH));
            $out .= PHP_EOL . "css_file('$filenames[$ix]');";
        }

        return $out;       
    }

    /*
        Extrae referencias a archivos .css del header de una pagina usando funciones de DOM

        Usar pero tener en cuenta que extractStyleUrls() puede funcionar mejor

        Ej:

        Array
        (
            [1] => https://practicatest.cl/dist/css/basic.min.css
            [2] => https://practicatest.cl/dist/css/style.themed.css
            [3] => https://practicatest.cl/static/fonts/css/fontawesome.css
            [4] => https://practicatest.cl/static/fonts/css/brands.css
            [5] => https://practicatest.cl/static/fonts/css/solid.css
            [6] => https://practicatest.cl/static/fonts/css/regular.css
            [7] => https://practicatest.cl/static/fonts/css/light.css
        )

        Si $use_helper es true, devuelve un string con el uso de css_file()

        Ej:

            css_file('https://practicatest.cl/dist/css/basic.min.css');
            css_file('https://practicatest.cl/dist/css/style.themed.css');
            css_file('https://practicatest.cl/static/fonts/css/fontawesome.css');
            css_file('https://practicatest.cl/static/fonts/css/brands.css');
            css_file('https://practicatest.cl/static/fonts/css/solid.css');
            css_file('https://practicatest.cl/static/fonts/css/regular.css');
            css_file('https://practicatest.cl/static/fonts/css/light.css');

    */
    static function extractLinkUrls(string $html, bool $use_helper = false) {
        $arr = HTML::getCSSLinks($html);

        if ($use_helper === false){
            return $arr;
        }

        $lines = [];
        foreach ($arr as $file){
            $lines[] = "css_file('$file');";           
        }

        return implode(PHP_EOL, $lines);
    }

    /*
        Usando expresiones regulares que recupera todas las urls de 

            <link rel='stylesheet> 
        
        de un string.
    */
    static function extractStyleUrls(string $html, $exp_cache = null) {
        if (Strings::startsWith('https://', $html) || Strings::startsWith('http://', $html)){
            $html = consume_api($html, 'GET', null, null, null, false, false, $exp_cache);
        } else {
            if (strlen($html) <= 255 && Strings::containsAny(['\\', '/'], $html)){
                if (file_exists($html)){
                    $html = Files::getContent($html);
                }            
            }
        }

        $pattern = "/<link\s+rel=['\"]stylesheet['\"].*?href=['\"](.*?)['\"].*?>/i";
        preg_match_all($pattern, $html, $matches);
        
        $styleUrls = array();
        foreach ($matches[1] as $match) {
            $styleUrls[] = $match;
        }
        
        return $styleUrls;
    }

    // Parsear fonts: https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/src
    // static function fontExtractor($html, $exp_cache = null) {
    //     // ...
    // }

     /*
        Devuelve todas las reglas de CSS donde sobre determinadas clases
        buscando dentro un path

        Ej:

            $path        = 'D:\www\woo2\wp-content\themes\kadence\assets\css\slider.min.css'; //  <--- archivo
            $css_classes = ['tns-slider', 'tns-item'];

            $css_rules   = CSSUtils::getCSSRules($path, $css_classes)

        o...

        Ej:

            $path        = 'D:\www\woo2\wp-content\themes\kadence\assets\css';  // <---- directory
            $css_classes = ['tns-slider', 'tns-item'];

            $css_rules   = CSSUtils::getCSSRules($path, $css_classes);

        Resultado

            Array
            (
                [0] => .tns-slider {transition: all 0s;}
                [1] => .tns-slider>.tns-item {box-sizing: border-box;}
                [2] => .tns-horizontal.tns-subpixel>.tns-item {display: inline-block;vertical-align: top;white-space: normal;}
                [3] => .tns-horizontal.tns-no-subpixel>.tns-item {float: left;}
                [4] => .tns-horizontal.tns-carousel.tns-no-subpixel>.tns-item {margin-right: -100%;}
                [5] => .tns-gallery>.tns-item {position: absolute;left: -100%;transition: opacity 0s,-webkit-transform 0s;transition: transform 0s,opacity 0s;transition: transform 0s,opacity 0s,-webkit-transform 0s;}
            )
    */
    static function getCSSRules(string $path, array $css_classes) {        
        if (!Strings::endsWith('.css', $path)){
            if (!is_dir($path)){
                throw new \InvalidArgumentException("Path should be a .css file or directory containing .css file(s)");
            }

            $files = Files::recursiveGlob($path . DIRECTORY_SEPARATOR . '*.css');
        } else {
            $files = [ $path ];
        }

        $rules = [];
        foreach ($files as $path){
            Stdout::pprint("Processing $path ...");

            $css    = static::beautifier($path);
            Stdout::pprint("Beautification done");

            $_rules = Strings::lines($css, true);
                        
            foreach ($_rules as $ix => $rule){
                if (!Strings::containsAny($css_classes, $rule)){
                    unset($_rules[$ix]);
                }
            }

            $rules = array_merge($rules, array_values($_rules));
        }

        return $rules;
    }

    /*
        Crear dentro de package asi puedo incluir dependencias dentro
        y no a nivel de proyecto completo

        "sabberworm/php-css-parser": "^8.4"
        
        @param  string $css    CSS en si o la ruta al archivo .css

        Ej:

        $path = 'D:\www\woo2\wp-content\themes\kadence\assets\css\slider.min.css';

        dd(
            CSSUtils::beautifier($path)
        );
    */
    static function beautifier(string $css) {
        if (Url::isValid($css) || (Strings::endsWith('.css', $css) && Files::exists($css))){
            $css = file_get_contents($css);
        }

        $parser          = new Parser($css);

        $css            = $parser->parse();
        $desminifiedCSS = $css->render();

        return $desminifiedCSS;
    }

    static function removeCSSClasses(string $html, $classesToRemove = null) : string {
        if (!empty($classesToRemove)) {
            foreach ($classesToRemove as $class) {
                $html = str_replace(" $class ", ' ', $html);
                $html = str_replace(" $class\"", ' "', $html);
            }

            return $html;
        }

        return preg_replace_callback('/<[^<>]*\sclass=[\'"`][^\'"`]*[\'"`][^<>]*>/i', function($match) {
            return preg_replace('/\sclass=[\'"`][^\'"`]*[\'"`]/i', '', $match[0]);
        }, $html);        
    }

    static function removeCSS(string $page, bool $remove_style_sections = true, bool $remove_css_inline = true) : string
     {
		// Eliminar CSS entre etiquetas <style></style>
		if ($remove_style_sections) {
			$page = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $page);
		}
		
		// Eliminar CSS inline dentro del atributo style=""
		if ($remove_css_inline) {
			$page = preg_replace('/style="[^"]*"/i', '', $page);
		}

        /*
            Elimino class="" vacios
        */

        $page = str_replace('class=""', '', $page);     
		
		return $page;
	}

    /*
        Devuelve un array con todas las clases de CSS utilizadas en el documento

        Puede servir para:

        - Luego solo seleccionar archivos CSS que las contengan o las reglas en especifico
        - Determinar si se utiliza un framework como Bootstrap (col-??-??, etc)

        Usar en combinacion HTML::getIDs()
    */
    static function getCSSClasses(string $html) {
        $dom = XML::getDocument($html);

        $xpath = new \DOMXPath($dom);
        $classNodes = $xpath->query('//*[@class]');

        $cssClasses = array();
        foreach ($classNodes as $node) {
            $classes = explode(' ', $node->getAttribute('class'));
            $cssClasses = array_merge($cssClasses, $classes);
        }

        $cssClasses = array_unique($cssClasses);
        return $cssClasses;
    }
    
}

