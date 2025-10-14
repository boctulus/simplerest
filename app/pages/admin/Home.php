<?php

namespace Boctulus\Simplerest\Pages\Admin;


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