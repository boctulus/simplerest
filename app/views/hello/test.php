<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

        <div class="accordion-flush accordion accordion-flush" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button type="button" class="btn btn-primary accordion-button collapsed" data-bs-target="#flush-collapseOne">Accordion Item #1
                    </button>
                </h2>
                <div class="accordion-flush accordion-collapse collapse">
                    <div class="accordion-body">
                        Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button type="button" class="btn btn-primary accordion-button collapsed" data-bs-target="#flush-collapseTwo">Accordion Item #2
                    </button>
                </h2>
                <div class="accordion-flush accordion-collapse collapse">
                    <div class="accordion-body">
                        Placeholder 2
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button type="button" class="btn btn-primary accordion-button collapsed" data-bs-target="#flush-collapseThree">Accordion Item #3
                    </button>
                </h2>
                <div class="accordion-flush accordion-collapse collapse">
                    <div class="accordion-body">
                        Placeholder 3
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>