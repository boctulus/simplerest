<?php

namespace simplerest\controllers;

use boctulus\hello_world\Hello;
use simplerest\controllers\MyController;

class Dumb2Controller extends MyController
{   
    function index()
    {
        dd(
            Hello::hi()
        );    
    }
}

