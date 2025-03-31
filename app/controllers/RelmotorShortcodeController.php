<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Modules\Relmotor\Relmotor;

class RelmotorShortcodeController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        set_template('templates/tpl_bt5.php');
        new Relmotor();
    }
}

