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

    function test_include()
    {
        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, ['sayBye', 'in_schema', 'doSomething'])
        );                   
    }

    function test_exclude()
    {
        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, [], ['tb_prefix', 'in_schema'])
        );                   
    }

    function test_interface_replacement()
    {
        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, [], [], ['sayHello', 'sayBye'])
        );                   
    }

    function test_interface_replacement_2()
    {
        $file = Files::getContent("D:\\laragon\\www\\simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, [], [], [], ['sayHello', 'sayBye'])
        );                   
    }
}

