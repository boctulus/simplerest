<?php

namespace simplerest\core;

use simplerest\core\libs\Cache;
use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\core\traits\ExceptionHandler;

class View
{
    use ExceptionHandler;

    static protected $head   = [];
    static protected $footer = [];

    const HEAD   = true;
    const FOOTER = false;

    protected $config;

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

            $expired = Cache::expiredFile($cached_path, $expiration_time);
            $cached  = !$expired;

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

    static function get_view_src(string $view_path, int $expiration_time = 0){
        if (!Strings::endsWith('.php', $view_path)){
            $view_path .= '.php';
        }

        if ($expiration_time === 0){
            return VIEWS_PATH . $view_path;
        }

        $cached_path = CACHE_PATH . 'views'. DIRECTORY_SEPARATOR . str_replace(['\\', '/'], '__dir__',  $view_path);
        $expired     = Cache::expiredFile($cached_path, $expiration_time);

        if (!$expired){
            $src = $cached_path;
        } else {
            $src = VIEWS_PATH . $view_path;
        }

        return $src;
    }

    static function get_view(string $view_path, int $expiration_time = 0){
        $path = Files::isAbsolutePath($view_path) ?  $view_path : VIEWS_PATH . $view_path;
        
        if (!Strings::endsWith('.php', $view_path)){
            $view_path .= '.php';
        }
        
        $cached_path = CACHE_PATH . 'views'. DIRECTORY_SEPARATOR . str_replace(['\\', '/'], '__dir__',  $view_path);

        $expired     = Cache::expiredFile($cached_path, $expiration_time);

        if (!$expired){
            $content = Files::reader($cached_path);
        } else {
            ob_start();
            include $path;
            
            $content = ob_get_contents();
            ob_end_clean();
            
            if ($expiration_time != 0){
                Files::writableOrFail($cached_path);
                $bytes = Files::writter($cached_path, $content);
            }
        }
        
        return $content;
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
        static $included;

        // if (!Strings::endsWith('.js', $file)){
        //     Logger::logError("Possible invalid file extension for '$file'. Expecting .js");
        // }

        $file_ori = $file;

        if (!isset($included) || $included == null){
            $included = [];
        } 

        if (in_array($file_ori, $included)){
            return;
        }

        $arr = $in_head ? 'head' : 'footer';

        if (Files::isAbsolutePath($file)){
            $file = str_replace(ROOT_PATH, base_url() . '/', $file);
            $file = str_replace('\\', '/', $file);
        } else {
            if (!Strings::startsWith('http', $file)){
                $file = '/public/assets/' . Strings::removeFirstSlash($file);
            } 
        }

        $included[] = $file_ori;

        static::$$arr['js'][] = [
            'file' => $file,
            'atts' => $atts
        ];
    }

    static function js(string $code, ?Array $atts = null, bool $in_head = false){
        $arr = $in_head ? 'head' : 'footer';
        static::$$arr['js'][] = $code;
    }

    static function css_file(string $file){
        static $included;

        if (!Strings::endsWith('.css', $file)){
            // Logger::logError("Possible invalid file extension for '$file'. Expecting .css");
        }

        $file_ori = $file;

        if (!isset($included) || $included == null){
            $included = [];
        } 

        if (in_array($file_ori, $included)){
            return;
        }

        if (Files::isAbsolutePath($file)){
            $file = str_replace(ROOT_PATH, base_url() . '/', $file);
            $file = str_replace('\\', '/', $file);
        } else {
            if (!Strings::startsWith('http', $file)){
                $file = '/public/assets/' . Strings::removeFirstSlash($file);
            } 
        }
     
        static::$head['css'][] = [
            'file' => $file
        ];

        $included[] = $file_ori;
    }

    static function css(string $file){
        static::$head['css'][] = $file;
    }

    function onCacheExpired(string $view_path){}

    function onCacheWritten(string $view_path){}
}