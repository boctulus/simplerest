<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="row mt-5">
  <div class="col-4 offset-4">

  <div id="list-example" class="list-group">
    <a class="list-group-item list-group-item-action" href="#list-item-1">Item 1</a>
    <a class="list-group-item list-group-item-action" href="#list-item-2">Item 2</a>
    <a class="list-group-item list-group-item-action" href="#list-item-3">Item 3</a>
    <a class="list-group-item list-group-item-action" href="#list-item-4">Item 4</a>
  </div>
  <div data-bs-spy="scroll" data-bs-target="#list-example" data-bs-offset="0" class="scrollspy-example" tabindex="0">
    <h4 id="list-item-1">Item 1</h4>
    <p>Texto bla bla 1</p>
    <h4 id="list-item-2">Item 2</h4>
    <p>Texto bla bla 2</p>
    <h4 id="list-item-3">Item 3</h4>
    <p>Texto bla bla 3</p>
    <h4 id="list-item-4">Item 4</h4>
    <p>Texto bla bla 4</p>
  </div>


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