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

    //echo tag('label')->for("nombre")->text("First nameee: ");
    echo tag('inputText')->name('nombre');

    //echo tag('label')->for("apellido")->text("Apellido")->class('mt-4');
    echo tag('inputText')->name('apellido')->placeholder("apellido");
    
    //echo tag('label')->for("edad")->text("Edad")->class('mt-4');
    echo tag('range')->name('edad')->min(0)->max(99)->default(10)->class('my-3');
    
    //echo tag('label')->for("exp")->text("Experiencia")->class('mt-4');;
    echo tag('range')->name('exp')->min(0)->max(99)->default(30)->class('my-3');



