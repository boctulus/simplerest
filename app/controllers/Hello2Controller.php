<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Html;
use simplerest\core\libs\Form;

class Hello2Controller extends Controller
{
    function __construct(){
        parent::__construct();
    }

    function index(){        
        $f = new Form();

        $f->h(3, "Datos");
        
        $f->div(function($form){
            $form->span('@', [
                'id'    => 'basic-addon',
                'class' => 'input-group-text'
            ]);
            $form->text('nombre', null, [
                "placeholder" => "Username"
            ]);
        }, [
            "class" => "input-group mb-3"
        ]);

        $f->select('sexo', 'varon', [
            'varon' => 1,
            'mujer' => 2
        ]);

        $f->label("edad", "Edad");
        $f->range('edad', 0, 99, 10);

        $f->radio("civil", "casado");
        $f->radio("civil", "soltero");

        $f->checkbox("hijos", "Hijos", true);

        $f->url("Linkedin");

        $f->label("comment", "Algo que desea agregar:");
        $f->area('comment', 'bla bla');

        $f->button("comprar", "Comprar");

        $f->reset("limpiar", "limpiar");
        $f->submit("enviar", "enviar");

        $f->br();

        $f->link_to("www.solucionbinaria.com", 'SolucionBinaria .com', [
            'class' => 'mb-3'
        ]);

        $this->view('hello2.php', [
            'formx' => $f->render()
        ]);
    }

    function xy(){
        $f = new Html();

        $f->macro('salutor', function($args)
        {
            return "<span/>Hello {$args[0]}</span>";
        });

        d($f->salutor("Isabel"));
    }
}

