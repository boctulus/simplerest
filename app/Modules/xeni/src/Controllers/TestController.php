<?php

namespace Boctulus\Simplerest\Modules\xeni\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class TestController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }

    function first_test(){
        return "First test of ". __CLASS__;
    }
}

