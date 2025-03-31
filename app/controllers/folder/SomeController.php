<?php

namespace Boctulus\Simplerest\Controllers\folder;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class SomeController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }

    function inc($val)
    {
        $res = (float) $val + 1;
        response()->send($res);
    }
    
    function inc2($val)
    {
        $res = (float) $val + 1;
        return $res;
    }

    function inc3($val)
    {
        $res = (float) $val + 1;
        response($res);
    }
}

