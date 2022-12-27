<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Html::class);

Bt5Form::setIdAsName();

/*
    Bootstrap 5 validation

    https://getbootstrap.com/docs/5.0/forms/validation/
*/

js_file('js/bootstrap/bt_validation.js');

?>

<h3>Bt5 Form validation</h3>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <?php

    /*
        Ver Bootstrap Validation !
    */

    echo tag('form')
    ->class('needs-validation')
    ->novalidate()
    ->content([        
        tag('label')->for("nombre")->text("Nombre"),
        tag('inputText')->name('nombre')->required(),

        tag('label')->class('mt-3')->for("apellido")->text("Apellido"),
        tag('inputText')->name('apellido')->placeholder("apellido"),

        tag('label')->class('mt-3')->for("edad")->text("Edad"),
        tag('range')->name('edad')->min(0)->max(99)->default(10),

        tag('label')->class('mt-3')->for("exp")->text("Experiencia"),
        tag('range')->name('exp')->min(0)->max(99)->default(30),
        
        tag('submit')->class('mt-3')->id("enviar")->value("Enviar")->success()
    ]);


    ?>
    
    </div>
</div>
