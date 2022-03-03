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

        <div class="col-sm-6">
          <?= 
          
          tag('ionSlider')->id('range_1')
          ->min(0)
          ->max(6000)

          ->value(50) 
          //->from(50)
          ->postfix(" &euro;")
          ->step(10)
          
          ?>

        </div>

        <div class="col-sm-6">
          <?= 
          
          tag('ionSlider')->id('range_2')
          ->min(-100)
          ->max(400)          
          ->postfix(" C")
          ->step(1)

          ->type('double')
          ->from(100)
          ->to(200)
          
          ?>
        </div>
            
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->



    <?php
    include_js(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js');
    include_js(ASSETS_PATH . 'adminlte/plugins/bootstrap-slider/bootstrap-slider.min.js');
      
    ?>


  <script>
    $(function () {
     

      /* ION SLIDER */
      //$('#range_1').ionRangeSlider({})
      //$('#range_2').ionRangeSlider({})

    })
  </script>

