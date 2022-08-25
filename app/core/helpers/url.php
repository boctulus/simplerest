<?php

use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\core\Model;
use simplerest\core\libs\Url;

/*
    Returns BASE_URL to be used in the FrontEnd
*/
function base_url(){
    static $base_url;

    if ($base_url !== null){
        return $base_url;
    }

    $base_url = Url::getHostname(Url::currentUrl()) . config()['BASE_URL'];
    
    if (!Strings::endsWith('/', $base_url)){
        $base_url .= "/";
    }

    return $base_url;
}
