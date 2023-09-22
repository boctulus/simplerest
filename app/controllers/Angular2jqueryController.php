<?php

namespace simplerest\controllers;

use simplerest\core\Request;
use simplerest\core\libs\CSS;
use simplerest\core\libs\XML;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\controllers\MyController;
use simplerest\core\Angular;

class Angular2JqueryController extends MyController
{
    function run()
    {
        $path = 'D:\www\woo7\wp-content\plugins\rack_quoter\shortcodes\rack_quoter\views\last_step.html';

        $page = Files::getContent($path);

        $page = Angular::remove($path);
        $page = str_replace(" -mark ", '', $page);  // <--- debo quitar esas clases de CSS como sea de los radio
    
        file_put_contents('D:\www\simplerest\app\shortcodes\rack_quoter\views\racks.php', $page);

        echo file_get_contents('http://simplerest.lan/pallet_rack_quoter');
    }
}

