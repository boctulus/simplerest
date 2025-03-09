<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\CodeReducer;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Files;
use simplerest\core\traits\TimeExecutionTrait;

class TestController extends Controller
{
    function __construct() { parent::__construct(); }

    function index()
    {
        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduce($file, ['tb_prefix'])
        );                   
    }
}

