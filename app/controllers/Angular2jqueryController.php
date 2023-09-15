<?php

namespace simplerest\controllers;

use simplerest\core\Request;
use simplerest\core\libs\CSS;
use simplerest\core\libs\XML;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\controllers\MyController;
use simplerest\core\Angular;

class Angular2jqueryController extends MyController
{
    function run()
    {
        $path = 'D:\www\simplerest\app\views\racks\racks.old';

        $page = Files::getContent($path);

        $page = Angular::remove($path);
    
        file_put_contents('D:\www\simplerest\app\views\racks\racks.php', $page);

        echo file_get_contents('http://simplerest.lan/pallet_rack_quoter');
    }
}

