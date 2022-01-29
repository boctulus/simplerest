<?php

namespace simplerest\controllers;

use simplerest\core\controllers\ConsoleController;
use simplerest\core\libs\Form;

class Hello2Controller extends ConsoleController
{
    function __construct(){
    }

    function index(){
        $f = new Form();

        $f->text('nombre', null, [
            'style' => "background-color:blue"
        ]);

        $f->select('sexo', 'varon', [
            'varon' => 1,
            'mujer' => 2
        ]);

        $f->range('edad', 0, 99, 10);

        $f->area('comment', 'bla bla');
        $f->url("Linkedin");

        $f->color("color", "Color");

        $f->reset("limpiar", "limpiar");
        $f->submit("enviar", "enviar");

        $f->link_to("www.google.com", 'Google');

        return $f->render();
    }
}

