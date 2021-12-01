<?php

namespace simplerest\controllers\some_folder;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;

class SomeCoolUltraController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        echo "jaaaa";            
    }
}

