<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;

Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

echo tag('shadow')
->content([
    '<div class="text-center text-uppercase">
        <h4>Información importante</h4>
    </div>',
    '<div class="text-center">
        <p>Esto es un texto demo para modulo de información importante</p>
    </div>'
])
->class("mt-3 p-3 mb-5");
?>

<div class="row">
    <div class="col-sm-4">
        <div class="card zooming-card shadow py-3">
            <div class="bd-vertical-align-wrapper">
                <div class=" bd-joomlaposition-20 clearfix">
                    <div class=" bd-block-17 bd-own-margins ">

                        <div class="text-center text-uppercase">
                            <h4>Emergencias</h4>
                        </div>

                        <div class="bd-blockcontent bd-tagstyles bd-custom-button">
                            <div class="custom">
                                <p><img class="bd-imagelink-3 bd-own-margins bd-imagestyles" style="display: block; margin-left: auto; margin-right: auto;" src="<?= asset('img/icons/fire_icon.jpg') ?>" width="80"></p>
                                <p class=" bd-textblock-101 bd-content-element" style="text-align: center;">Incendios, planes de emergencia,&nbsp;<br>etc ..</p>
                                <p style="text-align: center;"><a class="btn btn-primary" href="/emergencias"> ABRIR</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card zooming-card shadow py-3">
            <div class="bd-vertical-align-wrapper">
                <div class=" bd-joomlaposition-19 clearfix">
                    <div class=" bd-block-16 bd-own-margins ">

                        <div class="text-center text-uppercase">
                            <h4>Catalogo</h4>
                        </div>

                        <div class="bd-blockcontent bd-tagstyles">
                            <div class="custom">
                                <p><img class="bd-imagelink-54 bd-own-margins bd-imagestyles   " style="display: block; margin-left: auto; margin-right: auto;" src="<?= asset('img/icons/teams_icon.jpg') ?>" width="80"></p>
                                <p class=" bd-textblock-105 bd-content-element" style="text-align: center;">Medios humanos, medios materiales, recursos,<br>etc ..</p>
                                <p style="text-align: center;"><a class="btn btn-primary" href="/catalogo"> ABRIR</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card zooming-card shadow py-3">
            <div class="bd-vertical-align-wrapper">
                <div class=" bd-joomlaposition-18 clearfix">
                    <div class=" bd-block-15 bd-own-margins ">

                        <div class="text-center text-uppercase">
                            <h4>Inventario</h4>
                        </div>

                        <div class="bd-blockcontent bd-tagstyles">
                            <div class="custom">
                                <p><img class="bd-imagelink-56 bd-own-margins bd-imagestyles   " style="display: block; margin-left: auto; margin-right: auto;" src="<?= asset('img/icons/uniform_icon.jpg') ?>" width="80"></p>
                                <p class=" bd-textblock-109 bd-content-element" style="text-align: center;">Uniformes, EPIs, accesorios,&nbsp;<br>herramientas, etc...</p>
                                <p style="text-align: center;"><a class="btn btn-primary" href="/inventario"> ABRIR</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>