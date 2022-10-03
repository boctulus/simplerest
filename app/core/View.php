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

        $filename = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__dir__',  $view_path);

        switch ($expiration_time){
            case -1:
                $expired = false;
            break;
            case 0:
                $expired = true;
            break;    
            default:
                $ct      = @filemtime($filename);
                $expired = time() > $ct + $expiration_time;                
        }

        $cached  = !$expired;

        if ($expiration_time != 0){
            $file_exists = file_exists($filename);
        } else {
            $expiration_time = null;
        }

        if ($cached && $file_exists){
            $src = $filename;
        } else {
            $src = VIEWS_PATH . $view_path;
        }

        return $src;
    }

    static function get_view(string $view_path, int $expiration_time = 0){
        if (!Strings::endsWith('.php', $view_path)){
            $view_path .= '.php';
        }

        $filename = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__dir__',  $view_path);

        switch ($expiration_time){
            case -1:
                $expired = false;
            break;
            case 0:
                $expired = true;
            break;    
            default:
                $ct      = @filemtime($filename);
                $expired = time() > $ct + $expiration_time;                
        }

        $cached  = !$expired;

        if ($expiration_time != 0){
            $file_exists = file_exists($filename);
        } else {
            $expiration_time = null;
        }

        if ($cached && $file_exists){
            $content = Files::reader($filename);
        } else {
            $content = Files::reader(VIEWS_PATH . $view_path);
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

        $filename = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__dir__',  $view_path);

        switch ($expiration_time){
            case -1:
                $expired = false;
            break;
            case 0:
                $expired = true;
            break;    
            default:
                $ct      = @filemtime($filename);
                $expired = time() > $ct + $expiration_time;                
        }
        
        if ($expired && $expiration_time != 0){
            $this->onCacheExpired($view_path);
        }

        $cached  = !$expired;

        if ($expiration_time != 0){
            $file_exists = file_exists($filename);
        } else {
            $expiration_time = null;
        }

        if ($cached && $file_exists){
            $content = Files::reader($filename);
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

        if ($expiration_time != 0 && ($expired || !$file_exists)){
            Files::writableOrFail($filename);

            $bytes = Files::writter($filename, $content);

            if ($bytes != 0){
                $this->onCacheWritten($view_path);
            }
        }

        $path = VIEWS_PATH . "$layout";

        if (!file_exists($path)){
            response("Path '$path' not found", 404);
        }

        include $path; 
    }

    static function destroyCache(string $view_path) : bool {
        $filename = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__dir__',  $view_path);

        return Files::delete($filename);
    }

    static function getHead(){
        return static::$head;
    }

    static function getFooter(){
        return static::$footer;
    }
    
    static function js_file(string $file, ?Array $atts = null, bool $in_head = false){
        $arr = $in_head ? 'head' : 'footer';

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