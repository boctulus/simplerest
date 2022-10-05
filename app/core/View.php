<?php

namespace simplerest\core;

use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\traits\ExceptionHandler;

class View
{
    use ExceptionHandler;

    static protected $head   = [];
    static protected $footer = [];

    const HEAD   = true;
    const FOOTER = false;

    static function get_view_src(string $view_path, int $expiration_time = 0){
        if (!Strings::endsWith('.php', $view_path)){
            $view_path .= '.php';
        }

        if ($expiration_time === 0){
            return VIEWS_PATH . $view_path;
        }

        $cached_path = CACHE_PATH . 'views'. DIRECTORY_SEPARATOR . str_replace(['\\', '/'], '__dir__',  $view_path);

        $file_exists = null;

        switch ($expiration_time){
            // nunca expira
            case -1:
                $expired = false;
            break;
            // nunca se cachea
            case 0:
                $expired = true;
            break;    
            default:
                $file_exists = file_exists($cached_path);

                if (!$file_exists){
                    $expired = true;
                } else {
                    $_ct     = filemtime($cached_path);
                    $expired = time() > $_ct + $expiration_time;
                }            
        }

        $cached  = !$expired;

        if ($expiration_time != 0){
            $file_exists = file_exists($cached_path);
        } else {
            $expiration_time = null;
        }

        if ($cached){
            $src = $cached_path;
        } else {
            $src = VIEWS_PATH . $view_path;

            $content = Files::reader($src);

            if ($expiration_time != 0){
                Files::writableOrFail($cached_path);
                $bytes = Files::writter($cached_path, $content);
            }
        }

        return $src;
    }

    static function get_view(string $view_path, int $expiration_time = 0){
        if (!Strings::endsWith('.php', $view_path)){
            $view_path .= '.php';
        }
        
        if ($expiration_time === 0){
            return Files::reader(VIEWS_PATH . $view_path);
        }

        $file_exists = null;
        $cached_path = CACHE_PATH . 'views'. DIRECTORY_SEPARATOR . str_replace(['\\', '/'], '__dir__',  $view_path);

        switch ($expiration_time){
            // nunca expira
            case -1:
                $expired = false;
            break;
            // nunca se cachea
            case 0:
                $expired = true;
            break;    
            default:
                $file_exists = file_exists($cached_path);

                if (!$file_exists){
                    $expired = true;
                } else {
                    $_ct     = filemtime($cached_path);
                    $expired = time() > $_ct + $expiration_time;
                }            
        }

        $cached = !$expired;    

        if ($cached){
            $content = Files::reader($cached_path);
        } else {
            $content = Files::reader(VIEWS_PATH . $view_path);

            if ($expiration_time != 0){
                Files::writableOrFail($cached_path);
                $bytes = Files::writter($cached_path, $content);
            }
        }

        return $content;
    }

    function __construct(string $view_path, array $vars  = null, ?string $layout = null, int $expiration_time = 0)
    {
		$this->config = config();

		if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }

        if (empty($layout)){
            $layout = $this->config['template'];
        } 

        $layout_path = VIEWS_PATH . "$layout";

        if (!file_exists($layout_path)){
            response("Layout path '$layout_path' not found", 404);
        }

        if ($expiration_time !== 0){
            $cached_path = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__dir__',  $view_path);

            switch ($expiration_time){
                case -1:
                    $expired = false;
                break;
                case 0:
                    $expired = true;
                break;    
                default:
                    $file_exists = file_exists($cached_path);

                    if (!$file_exists){
                        $expired = true;
                    } else {
                        $_ct     = filemtime($cached_path);
                        $expired = time() > $_ct + $expiration_time;
                    }                  
            } 

            $cached = !$expired;

            if ($expired){
                $this->onCacheExpired($view_path);
            }
        } else {
            $cached = false;
        }
       
        if ($cached){
            $content = Files::reader($cached_path);
        } else {          
            if (!empty($vars)){
                extract($vars);
            }      

            ob_start();
            include VIEWS_PATH . $view_path;
            
            $content = ob_get_contents();
            ob_end_clean();

            $footer =  static::$footer;
            $head   =  static::$head;            
        }

        if ($expiration_time != 0 && !$cached){
            Files::writableOrFail($cached_path);
            $bytes = Files::writter($cached_path, $content);

            if ($bytes != 0){
                $this->onCacheWritten($view_path);
            }
        }

        include $layout_path; 
    }

    static function destroyCache(string $view_path) : bool {
        $cached_path = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__dir__',  $view_path);

        return Files::delete($cached_path);
    }

    static function getHead(){
        return static::$head;
    }

    static function getFooter(){
        return static::$footer;
    }
    
    static function js_file(string $file, ?Array $atts = null, bool $in_head = false){
        $arr = $in_head ? 'head' : 'footer';

        if (!Strings::startsWith('http', $file)){
            $file = '/public/assets/' . Strings::removeFirstSlash($file);
        }

        static::$$arr['js'][] = [
            'file' => $file,
            'atts' => $atts
        ];
    }

    static function js(string $file, ?Array $atts = null, bool $in_head = false){
        $arr = $in_head ? 'head' : 'footer';
        static::$$arr['js'][] = $file;
    }

    static function css_file(string $file){
        static::$head['css'][] = [
            'file' => $file
        ];
    }

    static function css(string $file){
        static::$head['css'][] = $file;
    }

    function onCacheExpired(string $view_path){}

    function onCacheWritten(string $view_path){}
}