<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\code_cleaner\AngularCleaner;
use simplerest\core\libs\CSS;
use simplerest\core\libs\Files;

class Angular2jqueryController extends Controller
{
    function run()
    {
        $path = 'D:\www\woo7\wp-content\plugins\rack_quoter\shortcodes\rack_quoter\views\last_step.html';

        $page = Files::getContent($path);

        $page = AngularCleaner::remove($path);
        $page = str_replace(" -mark ", '', $page);  // <--- debo quitar esas clases de CSS como sea de los radio
    
        file_put_contents('D:\www\simplerest\app\shortcodes\rack_quoter\views\racks.php', $page);

        echo file_get_contents('http://simplerest.lan/pallet_rack_quoter');
    }
}

