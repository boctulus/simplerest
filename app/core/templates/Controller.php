<?php

namespace __NAMESPACE;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;

class __NAME__ extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

