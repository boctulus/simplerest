<?php

namespace Boctulus\Simplerest\pages\admin;


class Home  /* extends Page */
{
    public $tpl_params = [
        'title'      => 'Home Title',
        'page_name'  => 'Home Page Name'
    ];

    function index(){
        return "Some contenteeeeeeeeeent";
    }
}