<?php

namespace __NAMESPACE__;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class __NAME__ extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

