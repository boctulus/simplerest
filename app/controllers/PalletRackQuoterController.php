<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Modules\rack_quoter\RackQuoterShortcode;

class PalletRackQuoterController extends Controller
{
    function index()
    {  
        set_template('templates/tpl_bt3.php');          
        render(RackQuoterShortcode::get());
    }
}

