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

                  
    <div class="row mt-5">
      <div class="col-6 offset-3 mt-5">

            
           <!-- input states -->
           <div class="form-group">
            <label class="col-form-label" for="inputSuccess"><i class="fas fa-check"></i> Input with
              success</label>
              <?= tag('inputText')->class("is-valid")->placeholder("Enter ...")->id("id1"); ?>
          </div>

          <div class="form-group">
            <label class="col-form-label" for="inputWarning"><i class="far fa-bell"></i> Input with
              warning</label>
              <?= tag('inputText')->class("is-warning")->placeholder("Enter ...")->id("id2"); ?>
          </div>
          
          <div class="form-group">
            <label class="col-form-label" for="inputError"><i class="far fa-times-circle"></i> Input with
              error</label>
              <?= tag('inputText')->class("is-invalid")->placeholder("Enter ...")->id("id3"); ?>
          </div>



      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->



    <?php

    //include_js(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js');
    //include_js(ASSETS_PATH . 'adminlte/plugins/bootstrap-slider/bootstrap-slider.min.js');

    include_js(ASSETS_PATH . 'adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js');
    
      
    ?>


  <script>
    $(function () {
    

    })
  </script>

