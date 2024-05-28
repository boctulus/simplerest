<?php

namespace simplerest\controllers;

use boctulus\hello_world\Hello;
use simplerest\core\controllers\Controller;

class Dumb2Controller extends Controller
{   
    function index()
    {
        dd(
            Hello::hi()
        );    
    }
}

