<?php

namespace simplerest\controllers\api;

global $api_version;

if (!file_exists(CORE_PATH . 'api'. DIRECTORY_SEPARATOR . $api_version . DIRECTORY_SEPARATOR . 'ApiController.php')){  
    \simplerest\core\Response::getInstance()->sendError("Unsupported API version for $api_version", 400);
}

class_alias("\simplerest\core\api\\$api_version\MySelf", 'simplerest\controllers\api\Myself');

class Me extends MySelf
{ 
    function __construct(){
        parent::__construct();  
    }
     
}  