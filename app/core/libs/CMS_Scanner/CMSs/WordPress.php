<?php

namespace simplerest\core\libs\CMS_Scanner\CMSs;

use simplerest\core\libs\HTML;
use simplerest\core\libs\XML;
use simplerest\core\libs\Strings;

class WordPress
{
    static function isIt($content){
        return Strings::containsAny(['/wp-includes/', '/wp-content/plugins/'], $content);
    }

    /*
        TO-DO

        Leer los metadatos del archivo "index" de c/ plugin y asi saber 
        el nombre en cada caso
    */
    static function getPlugins($content){
        $js_links = XML::extractNodes($content, '//script', 'src');

        $plugins = [];
        foreach($js_links as $link){
            $after = Strings::after($link, '/wp-content/plugins/');
            
            if (empty($after)){
                continue;
            }

            $plugins[] = Strings::before($after, '/');
        }

        $plugins = array_values(array_unique($plugins));

        return $plugins;
    }

    /*
        TO-DO

        Leer los metadatos del archivo "index" del theme y asi saber 
        el nombre del theme
    */
    static function getTheme($content){
        $css_links = HTML::getCSSLinks($content); 

        $theme = null;
        foreach($css_links as $link){
            $after = Strings::after($link, '/wp-content/themes/');
            
            if (empty($after)){
                continue;
            }

            $theme = Strings::before($after, '/');
            break;
        }

        if (!empty($theme)){
            return $theme;
        }

        $js_links = XML::extractNodes($content, '//script', 'src');

        $theme = null;
        foreach($js_links as $link){
            $after = Strings::after($link, '/wp-content/themes/');
            
            if (empty($after)){
                continue;
            }

            $theme = Strings::before($after, '/');
            break;
        }

        return $theme;
    }
}

