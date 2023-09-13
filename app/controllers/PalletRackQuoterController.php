<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class PalletRackQuoterController extends MyController
{
    function index()
    { 
        css_file('vendors/bootstrap/3.x/normalize.css');
        css_file('racks/racks.css');
        
        view('racks\racks', null, 'templates/tpl_bt3.php');              
    }
}

