<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Html::class);

Bt5Form::setIdAsName();

?>

<h3>Bt5 Form validation</h3>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <?php

    echo tag('form')->novalidate()->content([        
        tag('label')->for("nombre")->text("Nombre"),
        tag('inputText')->name('nombre'),

        tag('label')->for("apellido")->class('mt-3')->text("Apellido"),
        tag('inputText')->name('apellido')->placeholder("apellido"),

        tag('label')->for("edad")->class('mt-3')->text("Edad"),
        tag('range')->name('edad')->min(0)->max(99)->default(10),

        tag('label')->for("exp")->class('mt-3')->text("Experiencia"),
        tag('range')->name('exp')->min(0)->max(99)->default(30),
    ]);



