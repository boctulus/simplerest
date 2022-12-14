<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Html::class);

Bt5Form::setIdAsName();

/*
    Bootstrap 5 validation

    https://getbootstrap.com/docs/5.0/forms/validation/
*/

js_file('js/boostrap/bt_validation.js');

?>

<h3>Bt5 Form validation</h3>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <?php

    /*
        Boostrap Validation !

        Incluye textos que seran mostrados si el campo valida o no valida
    */

    echo tag('form')
    ->class('g-4 needs-validation row') // g-* sirce para el interlineado -ver "gutters"-
    ->novalidate()
    ->content([        

        tag('div')->class('col-md-6 position-relative')
        ->content([
            tag('label')->for("nombre")->text("Nombre"),
            tag('inputText')->name('nombre')->required(),
            tag('div')->class('invalid-tooltip')->content('Campo obligatorio'), // texto si valida
            tag('div')->class('valid-tooltip')->content('Luce bien'), // texto sino valida
        ]),

        tag('div')->class('col-md-6 position-relative')
        ->content([
            tag('label')->for("apellido")->text("Apellido"),
            tag('inputText')->name('apellido')->placeholder("apellido")->required(),
            tag('div')->class('invalid-tooltip')->content('Campo obligatorio'),
            tag('div')->class('valid-tooltip')->content('Luce bien'),
        ]),
                 
        tag('div')->class('col-md-12 position-relative')
        ->content([
            tag('label')->for("edad")->text("Edad"),
            tag('range')->name('edad')->min(0)->max(99)->default(10),
        ]),

        tag('div')->class('col-md-12 position-relative')
        ->content([
            tag('label')->for("exp")->text("Experiencia"),
            tag('range')->name('exp')->min(0)->max(99)->default(30),
        ]),

        tag('div')->class('col-md-12 position-relative')
        ->content([
            tag('submit')->class('col-md-12')->id("enviar")->value("Enviar")->success()
        ]),
        
        
    ]);

    ?>
    
    </div>
</div>
