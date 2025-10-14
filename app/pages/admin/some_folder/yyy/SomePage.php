<?php

namespace Boctulus\Simplerest\Pages\Admin\SomeFolder\Yyy;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;

class SomePage
{
    public $page_name = 'Some Page';
    public $title     = 'Some Page | Example';

    function __construct()
    {   
        
    }

    function index(){
        return "Some content";
    }
}