<?php

namespace simplerest\pages\admin;

use simplerest\abstracts\pages\Page;

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