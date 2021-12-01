<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;

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

