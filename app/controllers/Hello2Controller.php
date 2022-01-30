<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Form;

class Hello2Controller extends Controller
{
    function __construct(){
        parent::__construct();
    }

    function index(){        
        $f = new Form();
        
        $f->div(function($form){
            $form->span('@', [
                'id'    => 'basic-addon',
                'class' => 'input-group-text'
            ]);
            $form->text('nombre');
        }, [
            "class" => "input-group mb-3"
        ]);

        $f->select('sexo', 'varon', [
            'varon' => 1,
            'mujer' => 2
        ]);

        $f->range('edad', 0, 99, 10);

        $f->radio("civil", "casado");
        $f->radio("civil", "soltero");

        $f->checkbox("hijos", "Hijos", true);

        $f->area('comment', 'bla bla');
        $f->url("Linkedin");

        $f->button("comprar", "Comprar");

        $f->reset("limpiar", "limpiar");
        $f->submit("enviar", "enviar");

        $f->link_to("www.google.com", 'Google');

        $this->view('hello2.php', [
            'formx' => $f->render()
        ]);
    }
}

