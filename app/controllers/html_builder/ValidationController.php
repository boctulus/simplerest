<?php

namespace simplerest\controllers\html_builder;

use simplerest\core\controllers\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

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
        \simplerest\core\libs\HtmlBuilder\Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Html::class);
        \simplerest\core\libs\HtmlBuilder\Bt5Form::setIdAsName();

        $req = true;

        $html = tag('inputText')
        ->name('nombre')
        ->when($req, function($o){
            $o->required('required');
        });

        dd($html->render());
    }

}

