<?php

namespace simplerest\core;

use simplerest\core\traits\ExceptionHandler;
use simplerest\core\libs\Files;

class View
{
    use ExceptionHandler;

    const LAYOUT = 'app_layout.php';


    function __construct(string $view_path, array $vars_to_be_passed  = null, ?string $layout = null, int $expiration_time = 0)
    {
		$this->config = config();

		if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }

        if (empty($layout)){
            $layout = static::LAYOUT;
        }

        $filename   = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__DIR__',  $view_path);

        switch ($expiration_time){
            case -1:
                $expired = false;
            break;
            case -0:
                $expired = true;
            break;    
            default:
                $ct      = filemtime($filename);
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
            $content = file_get_contents($filename);
        } else {
            if (!empty($vars_to_be_passed))
            extract($vars_to_be_passed);      
            
            ob_start();
            include VIEWS_PATH . $view_path;
            $content = ob_get_contents();
            ob_end_clean();
        }

        if ($expiration_time != 0 && ($expired || !$file_exists)){
            Files::writableOrFail($filename);

            $bytes = @file_put_contents($filename, $content);

            if ($bytes != 0){
                $this->onCacheWritten($view_path);
            }
        }

        include VIEWS_PATH . "$layout"; 
    }

    static function destroyCache(string $view_path) : bool {
        $filename   = CACHE_PATH . 'views/'. str_replace(['\\', '/'], '__DIR__',  $view_path);

        return Files::delete($filename);
    }

    function onCacheExpired(string $view_path){}

    function onCacheWritten(string $view_path){}
}