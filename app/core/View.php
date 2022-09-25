<?php

namespace simplerest\core;

use simplerest\core\traits\ExceptionHandler;
use simplerest\core\libs\Files;

class View
{
    use ExceptionHandler;

    static protected $head   = [];
    static protected $footer = [];

    function __construct(string $view_path, array $vars_to_be_passed  = null, ?string $layout = null, int $expiration_time = 0)
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
            if (!empty($vars_to_be_passed)){
                extract($vars_to_be_passed);
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
    
    static function enqueue_js(string $file, ?Array $atts = null, bool $in_head = false){
        if ($in_head){
            static::$head['js'][] = [
                'file' => $file,
                'atts' => $atts
            ];
        } else {
            static::$footer['js'][] = [
                'file' => $file,
                'atts' => $atts
            ];
        }        
    }

    static function enqueue_css(string $file){
        static::$head['css'][] = [
            'file' => $file
        ];
    }

    function onCacheExpired(string $view_path){}

    function onCacheWritten(string $view_path){}
}