<?php

namespace simplerest\pages\admin\xxx\yyy;

use simplerest\core\libs\DB;
use simplerest\core\Request;

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