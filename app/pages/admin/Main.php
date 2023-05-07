<?php

namespace simplerest\pages\admin;

use Color;
use simplerest\pages\Page;
use simplerest\core\libs\DB;
use simplerest\core\Request;
use Google\Service\Docs\Background;

/*
    Pagina por defecto si estuviera asi seteada en el Controller
    como $default_page

*/
class Main extends Page
{
    public $tpl_params = [
        'title'      => 'Home Title',
        'page_name'  => 'Home Page Name',
    ];

    function index(){
        return "
            Bla bla --Index
        ";
    }
}