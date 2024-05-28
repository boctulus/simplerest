<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\shortcodes\rack_quoter\RackQuoterShortcode;

class PalletRackQuoterController extends Controller
{
    function index()
    {  
        set_template('templates/tpl_bt3.php');          
        render(RackQuoterShortcode::get());
    }
}

