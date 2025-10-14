<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\code_cleaner\AngularCleaner;
use Boctulus\Simplerest\Core\Libs\CSS;
use Boctulus\Simplerest\Core\Libs\Files;

class Angular2JqueryController extends Controller
{
    function run()
    {
        $path = 'D:\www\woo7\wp-content\plugins\rack_quoter\shortcodes\rack_quoter\views\last_step.html';

        $page = Files::getContent($path);

        $page = AngularCleaner::remove($path);
        $page = str_replace(" -mark ", '', $page);  // <--- debo quitar esas clases de CSS como sea de los radio
    
        file_put_contents('D:\www\Boctulus\Simplerest\app\shortcodes\rack_quoter\views\racks.php', $page);

        echo file_get_contents('http://simplerest.lan/pallet_rack_quoter');
    }
}

