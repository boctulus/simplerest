<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Form;

class Hello2Controller extends Controller
{
    function __construct(){
        parent::__construct();
    }

    function index(){
        $this->view('hello/hello2.php', null, null, 0);
    }
}

