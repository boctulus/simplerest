<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class SomeWordsController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        return 'palabras más o menos';              
    }

    function decir($palabra){
        return "otro/a $palabra";
    }
}

