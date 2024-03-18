<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;

class BzzImportController extends MyController
{
    function get_completion()
    {
       $data = [
        'completion' => 82
       ];

       response()->send($data);
    }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

