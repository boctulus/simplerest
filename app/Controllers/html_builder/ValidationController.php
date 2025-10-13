<?php

namespace Boctulus\Simplerest\Controllers\html_builder;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

class ValidationController extends Controller
{
    /*
        Basico  
    */
    function index(){
        view('html_builder_examples/validation');
    }

    function v1(){
        view('html_builder_examples/v1');
    }

    function v2(){
        view('html_builder_examples/v2');
    }

    function v2a(){
        view('html_builder_examples/v2a');
    }

    function v3(){
        \Boctulus\Simplerest\Core\Libs\HtmlBuilder\Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Html::class);
        \Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form::setIdAsName();

        $req = true;

        $html = tag('inputText')
        ->name('nombre')
        ->when($req, function($o){
            $o->required('required');
        });

        dd($html->render());
    }

}

