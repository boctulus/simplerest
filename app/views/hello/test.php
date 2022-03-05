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
    <div class="col-6 offset-3">

    <!-- <div class="form-group">
      <label>Multiple</label>
      <select class="duallistbox" multiple="multiple">
        <option selected>Alabama</option>
        <option>Alaska</option>
        <option>California</option>
        <option>Delaware</option>
        <option>Tennessee</option>
        <option>Texas</option>
        <option>Washington</option>
      </select>
    </div> -->

    <?php
      echo tag('select')
      ->id('comidas_duallistbox')
      ->placeholder('Tu comida favorita')
      ->options([
          'Pasta' => 'pasta',
          'Pizza' => 'pizza',
          'Asado' => 'asado',
          'Banana' => 'banana',
          'Frutilla' => 'frutilla'
      ])
      ->multiple()   
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
    
    //Bootstrap Duallistbox
    $('#comidas_duallistbox').bootstrapDualListbox()
  </script>

