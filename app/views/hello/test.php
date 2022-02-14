<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <?php
         echo 
                tag('blockquote')->content(
                    tag('p')->text(
                        'A well-known quote, contained in a blockquote element.'
                    ) .
                        tag('blockquoteFooter')->content('Someone famous in ' . tag('cite')->title("Source Title")->content('Source Title'))
                )->class('mb-0');
    ?>


    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>