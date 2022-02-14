<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

        <?php

        echo tag('modal')
        ->header(
            tag('modalTitle')->text('Modal title') . 
            tag('closeButton')->dataBsDismiss('modal')
        )
        ->body(
            tag('p')->text('Modal body text goes here!')
        )
        ->footer(
            tag('closeModal') .
            tag('button')->text('Save changes')
        )
        ->options(['fullscreen'])
        ->id('exampleModal');
                
        ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>