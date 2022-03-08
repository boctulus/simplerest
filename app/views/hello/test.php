<?php

use simplerest\core\libs\HtmlBuilder\AdminLte;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


//Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\AdminLte::class);

include_css(ASSETS_PATH . 'adminlte/dist/css/adminlte.css');

?>
<style>
  .input-group-append {
    cursor: pointer;
  }
</style>

  <section class="container">
    <h2 class="py-2">Datepicker in Bootstrap 5</h2>
    <form class="row">
      <label for="date" class="col-1 col-form-label">Date</label>
      <div class="col-5">
        <div class="input-group date" id="datepicker">
          <input type="text" class="form-control" id="date"/>
          <span class="input-group-append">
            <span class="input-group-text bg-light d-block">
              <i class="fa fa-calendar"></i>
            </span>
          </span>
        </div>
      </div>
    </form>
  </section>

<script>
  $(function(){
    $('#datepicker').datepicker();
  });
</script>

