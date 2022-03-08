
<!-- datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<!-- https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js -->
<script src="http://simplerest.lan:8082/public/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>


<?php

use simplerest\core\libs\HtmlBuilder\AdminLte;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


//Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\AdminLte::class);

include_css(ASSETS_PATH . 'adminlte/dist/css/adminlte.css');


?>
<style>
  
</style>


<div class="row">
    <div class="col-6 mt-3 offset-3">

                <div class="form-group">
                  <label>Date range:</label>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control float-right" id="reservation">
                  </div>
                  <!-- /.input group -->
                </div>

      <?php

        // echo tag('inputGroup')
        // ->content(
        //   tag('inputText')
        // )
        // ->prepend(
        //     tag('button')->info()->icon('calendar')
        // )->class('mb-3 date')->id("reservationdatetime");

      ?>
  

    </div>
</div>

<script>
  $(function(){
    $('#reservationdatetime').datepicker();

    //Date range picker
    $('#reservation').daterangepicker();
  });
</script>

