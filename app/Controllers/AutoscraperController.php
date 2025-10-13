<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

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