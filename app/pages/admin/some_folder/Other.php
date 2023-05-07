<?php

namespace simplerest\pages\admin\some_folder;

use simplerest\core\libs\DB;
use simplerest\core\Request;

class Other
{
    public $page_name = 'Some Page';
    public $title     = 'Some Page | Example';

    function __construct()
    {   
        
    }

    function index(){
        return "blllllllaa bla";
    }
}