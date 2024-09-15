<?php

namespace __NAMESPACE;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\core\traits\TimeExecutionTrait;

class __NAME__ extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

