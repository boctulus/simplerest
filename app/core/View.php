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

        $filename   = CACHE_PATH . $view_path;

        $ct      = filemtime($filename);
        $expired = time() > $ct + $expiration_time;
        $cached  = !$expired;

        $file_exists = file_exists($filename);

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

        if ($expired || !$file_exists){
            file_put_contents($filename, $content);
        }

        include VIEWS_PATH . "$layout"; 
    }

    static function destroyCache(string $view_path) : bool {
        $filename   = CACHE_PATH . $view_path;

        return Files::delete($filename);
    }
}