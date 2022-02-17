<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">


        <nav class="nav nav-tabs">            
            <a class="nav-link active" aria-current="page" href="#">Active</a>
           
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                    
                    <hr class="dropdown-divider">
                    
                    <a class="dropdown-item" href="#">Separated link</a>
                </div>
            </div>
            
            <a class="nav-link" href="#">Link</a>            
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
           
        </nav>


    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>