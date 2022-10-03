<?php

namespace simplerest\controllers;

use simplerest\core\View;
use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class AndreaController extends MyController
{
    function __construct()
    {
        $this->assets();
    }

    function index(){
        $this->builder();
    }

    protected function assets(){
        css_file(
            asset('andrea/css/master.css')
        );

        css_file(
            asset('andrea/css/header2.css')
        );

        css_file(
            asset('andrea/css/bookblock.css')
        );

        css('
            .main-slider_content { background-color:#FFCB0B; }
        ');
    }

    function builder()
    {   
        view('andrea/builder');
    }

    function more_content()
    {
        view('andrea/more_content');
    }
}

