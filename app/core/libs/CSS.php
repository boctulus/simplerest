<?php

namespace simplerest\core\libs;

use simplerest\core\libs\XML;
use simplerest\core\libs\Strings;
use Sabberworm\CSS\Parser;

class CSS
{
    /*
        Extrae todas las referencias a archivos .css del header de una pagina
    */
    static function extractLinkUrls(string $html) {
        return XML::extractLinkUrls($html, ['css']);
    }

     /*
        Devuelve todas las reglas de CSS donde sobre determinadas clases
        buscando dentro un path

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

        TODO

        El path debe poder ser no solo a un archivo sino a una carpeta
        y buscar recursivamente con glob() por ejemplo

        Ej:

            $path        = 'D:\www\woo2\wp-content\themes\kadence\assets\css\slider.min.css';
            $css_classes = ['tns-slider', 'tns-item'];

            $css_rules   = CSSUtils::getCSSRules($path, $css_classes)

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

}

