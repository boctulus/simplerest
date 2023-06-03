<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class TestImportCsvController extends MyController
{
    function index()
    {
       view('upload_csv.php');                
    }
}

