<?php

namespace simplerest\core;

use simplerest\core\traits\ExceptionHandler;
use simplerest\core\libs\Factory;

class View
{
    use ExceptionHandler;

    function __construct(string $view_path, array $vars_to_be_passed  = null, string $layout = 'app_layout.php')
    {
		$this->config = config();

		if ($this->config['error_handling']) {
            set_exception_handler([$this, 'exception_handler']);
        }

        if (!empty($vars_to_be_passed))
            extract($vars_to_be_passed);      
            
        ob_start();
        include VIEWS_PATH . $view_path;
        $content = ob_get_contents();
        ob_end_clean();

        include VIEWS_PATH . "$layout"; 
    }
}