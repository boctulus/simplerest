<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\CodeReducer;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\TimeExecutionTrait;

class TestController extends Controller
{
    function __construct() { parent::__construct(); }

    function mid(){
        return "Hello World!";        
    }

    function test_include()
    {
        $file = Files::getContent("D:\\laragon\\www\\Boctulus\\Simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, ['sayBye', 'in_schema', 'doSomething'])
        );                   
    }

    function test_exclude()
    {
        $file = Files::getContent("D:\\laragon\\www\\Boctulus\\Simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, [], ['tb_prefix', 'in_schema'])
        );                   
    }

    function test_interface_replacement()
    {
        $file = Files::getContent("D:\\laragon\\www\\Boctulus\\Simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, [], [], ['sayHello', 'sayBye'])
        );                   
    }

    function test_interface_replacement_2()
    {
        $file = Files::getContent("D:\\laragon\\www\\Boctulus\\Simplerest\\etc\\test.php");

        dd(
            (new CodeReducer())->reduceCode($file, [], [], [], ['sayHello', 'sayBye'])
        );                   
    }
}

