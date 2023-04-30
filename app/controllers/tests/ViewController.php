<?php

namespace simplerest\controllers\tests;

use simplerest\core\View;
use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class ViewController extends MyController
{
    function test_async_defer_1(){
        set_template('test_async_await/my_tpl_1.php');
        render("Hola Sr. Putin");
    }

    function test_async_defer_1b(){
        set_template('test_async_await/my_tpl_1b.php');
        render("Hola Sr. Putin");
    }

    function test_async_defer_1c(){
        set_template('test_async_await/my_tpl_1c.php');
        render("Hola Sr. Putin");
    }

    function test_async_defer_2(){
        set_template('test_async_await/my_tpl_2.php');
        render("Hola Sr. Putin");
    }

    function test_async_defer_3(){
        set_template('test_async_await/my_tpl_3.php');
        render("Hola Sr. Putin");
    }

    function test_async_defer_4(){
        set_template('test_async_await/my_tpl_4.php');
        render("Hola Sr. Putin");
    }

    function test_async_defer_5(){
        set_template('test_async_await/my_tpl_5.php');
        render("Hola Sr. Putin");
    }

    function test_asset_enqueue(){
       js_file('https://kit.fontawesome.com/3f60db90e4.js', [
            "crossorigin" => "anonymous" // falta incluir atributos
        ]);

        render("Hola Sr. Putin");
    }

    function test_asset_local(){
        js_file('js/dojo/dojo.js');

        render("Hola Sr. Putin");
    }
    
    /*
        Decorado de vistas 
    */
    function view_decoration()
    {  
        css_file(
            asset('andrea/css/master.css')
        );

        $placeholder = get_view('andrea/builder');
        $content     = get_view('andrea/container', ['placeholder' => $placeholder]);

        render($content);
    }

    function view_decoration_2()
    {  
        css_file(
            asset('andrea/css/master.css')
        );

        $content = '<section style="border: red 1px solid;">' .
            get_view('andrea/builder') .
        '</section>';

        render($content);
    }

    
}

