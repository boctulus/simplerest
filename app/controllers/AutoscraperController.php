<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class AutoscraperController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        view('autoscraper\main.php');                 
    }
}