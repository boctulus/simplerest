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


      <?php
        echo tag('select')
        ->id('sexo')
        ->options([
          'varon' => 1,
          'mujer' => 2
        ])
        ->default(1)
        ->placeholder('Su sexo')
        ->class('my-3');


      ?>


      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->



    <?php

    //include_js(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js');
    //include_js(ASSETS_PATH . 'adminlte/plugins/bootstrap-slider/bootstrap-slider.min.js');
    //include_js(ASSETS_PATH . 'adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js');
    
      
    ?>


  <script>
    $(function () {

      $('#single-select-field' ).select2( {
          theme: 'bootstrap-5'
      });


      $('#sexo' ).select2( {
          theme: 'bootstrap-5'
      });

    })
  </script>

