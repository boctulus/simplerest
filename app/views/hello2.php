<?php

use simplerest\core\libs\Form;

Form::macro('salutor', function($name, $adj)
{
    return "<span/>Hello $adj $name</span>";
});


$f = new Form();

$f->h(3, "Datos");

$f->tag('hr');

$f->p("Hola mundo cruel");

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

$f->inputButton("comprar", "Comprar");

$f->reset("limpiar", "limpiar");
$f->submit("enviar", "enviar");

$f->br();

$f->insert(Form::salutor("Isabel", "bella"));

$f->link_to("www.solucionbinaria.com", 'SolucionBinaria .com', [
    'class' => 'mb-3'
]);

echo $f->render();
