<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\shortcodes\relmotor\RelmotorShortcode;

class RelmotorShortcodeController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        $pr = new RelmotorShortcode();
        render($pr->index(), 'templates/tpl_bt5.php');                  
    }
}

