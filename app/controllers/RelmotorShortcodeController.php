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
        set_template('templates/tpl_bt5.php');
        new RelmotorShortcode();
    }
}

