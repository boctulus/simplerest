<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\shortcodes\rack_quoter\RackQuoterShortcode;

class PalletRackQuoterController extends MyController
{
    function index()
    {  
        set_template('templates/tpl_bt3.php');          
        render(RackQuoterShortcode::get());
    }
}

