<?php

use simplerest\core\libs\Bt5Form;
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <ol class="list-group list-group-numbered">
    <li class="list-group-item">Cras justo odio</li>
    <li class="list-group-item">Cras justo odio</li>
    <li class="list-group-item">Cras justo odio</li>
    </ol>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
        myModal.show()
    });
</script>