<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <?php
         echo tag('carousel')->content([
            tag('carouselItem')->content(
                tag('carouselImg')->src(assets('img/slide-1.jpeg'))
            )->caption(
                '<h5>First slide label</h5>
                <p>Some representative placeholder content for the first slide.</p>'
            ),

            tag('carouselItem')->content(
                tag('carouselImg')->src(assets('img/slide-2.jpeg'))
            ),

            tag('carouselItem')->content(
                tag('carouselImg')->src(assets('img/slide-3.jpeg'))
            )
        ])->id("carouselExampleControls")->withControls()->withIndicators()
        // ->dark()
        ;
    ?>


    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>