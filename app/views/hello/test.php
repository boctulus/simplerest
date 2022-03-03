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

        
      
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Ribbons</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
              <div class="row">

                    <?=

                      tag('ribbon')
                      ->bg('gray')
                      ->style('height: 100px')
                      ->title(
                        tag('ribbonTitle')->content('Ribbon')->bg('primary')
                      )
                      ->body(
                        'Ribbon Default <br />
                        <small>.ribbon-wrapper.ribbon-lg .ribbon</small>'
                      )

                    ?>

                    <?=

                      tag('ribbon')
                      ->bg('gray')
                      ->style('min-height: 300px')
                      ->class('mt-3')  
                      ->header(
                        tag('img')->src(asset('img/photo2.png'))->class('img-fluid py-3')
                      )                  
                      ->title(
                        tag('ribbonTitle')->content('Ribbon')->bg('danger')
                      )
                      ->body(
                        'Ribbon Default <br />
                        <small>.ribbon-wrapper.ribbon-lg .ribbon</small>'
                      )

                    ?>
                  
              </div>

              <!-- /.card-body -->
            </div>


            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>













      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->



    <?php
    //include_js(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js');
    //include_js(ASSETS_PATH . 'adminlte/plugins/bootstrap-slider/bootstrap-slider.min.js');
      
    ?>


  <script>
    $(function () {
    

    })
  </script>

