<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

        <?php

        echo tag('modal')->content(
            tag('modalDialog')->content(
                tag('modalContent')->content(
                    tag('modalHeader')->content(
                        tag('modalTitle')->text('Modal title') . 
                        tag('closeButton')->dataBsDismiss('modal')
                    ) .
                    tag('modalBody')->content(
                        tag('p')->text('Modal body text goes here.')
                    ) . 
                    tag('modalFooter')->content(
                        tag('closeModal') .
                        tag('button')->text('Save changes')
                    ) 
                ) 
            )
        )->id('exampleModal');
                
        ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>