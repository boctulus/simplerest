<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
  <div class="col-4 offset-4">

      <div class="card">
        <img src="<?= assets('img/warning.svg.png') ?>" width="300px" class="img-fluid" alt="...">

        <div class="card-body">
            <h5 class="card-title">Card title</h5>
            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
      </div>


        <div class="card" aria-hidden="true">
          <img src="<?= assets('img/warning.svg.png') ?>" width="300px" class="img-fluid" alt="...">
          <div class="card-body">
            <h5 class="card-title placeholder-glow">
              <span class="placeholder col-6"></span>
            </h5>
            <p class="card-text placeholder-glow">
              <span class="placeholder col-7"></span>
              <span class="placeholder col-4"></span>
              <span class="placeholder col-4"></span>
              <span class="placeholder col-6"></span>
              <span class="placeholder col-8"></span>
            </p>
            <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-3"></a>
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