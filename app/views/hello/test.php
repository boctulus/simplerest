<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
  <div class="col-4 offset-4">

    <!-- ok -->
    <button 
    type="button" 
    class="btn btn-lg btn-danger popovers" 
    data-bs-toggle="popover" 
    title="Popover title" 
    data-bs-placement = "bottom"
    data-bs-content="And here's some amazing content. It's very engaging. Right?">Click to toggle popover</button>


  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
    myModal.show()
  });

  var popover = new bootstrap.Popover(document.querySelector('.popovers'), {
  container: 'body'
  })
</script>