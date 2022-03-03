<?php

use simplerest\core\libs\HtmlBuilder\AdminLte;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


//Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\AdminLte::class);

include_css(ASSETS_PATH . 'adminlte/plugins/ion-rangeslider/css/ion.rangeSlider.min.css');
include_css(ASSETS_PATH . 'adminlte/plugins/bootstrap-slider/css/bootstrap-slider.min.css');
include_css(ASSETS_PATH . 'adminlte/dist/css/adminlte.css');


?>

<style>

</style>

                  
    <div class="row mt-5">
      <div class="col-6 offset-3">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Ion Slider</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row margin">
              <div class="col-sm-6">
                <input id="range_1" type="text" name="range_1" value="">
              </div>

              <div class="col-sm-6">
                <input id="range_2" type="text" name="range_2" value="1000;100000" data-bs-type="double"
                        data-step="500" data-postfix=" &euro;" data-from="30000" data-to="90000" data-hasgrid="true">
              </div>
            </div>
            <div class="row margin">
              <div class="col-sm-6">
                <input id="range_5" type="text" name="range_5" value="">
              </div>
              <div class="col-sm-6">
                <input id="range_6" type="text" name="range_6" value="">
              </div>
            </div>
            <div class="row margin">
              <div class="col-sm-12">
                <input id="range_4" type="text" name="range_4" value="10000;100000">
              </div>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
          <div class="col-6 offset-3">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Bootstrap Slider</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row margin">
                  <div class="col-sm-6">
                    <div class="slider-red">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="horizontal"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>

                    <p>.slider-red input.slider</p>
                    <div class="slider-blue">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="horizontal"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>

                    <p>.slider-blue input.slider</p>
                    <div class="slider-green">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="horizontal"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>

                    <p>.slider-green input.slider</p>
                    <div class="slider-yellow">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="horizontal"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>

                    <p>.slider-yellow input.slider</p>
                    <div class="slider-teal">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="horizontal"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>

                    <p>.slider-teal input.slider</p>
                    <div class="slider-purple">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="horizontal"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>

                    <p>.slider-purple input.slider</p>
                  </div>
                  <div class="col-sm-6 d-flex justify-content-center">
                    <div class="slider-red mx-3">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="vertical"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>
                    <div class="slider-blue mx-3">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="vertical"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>
                    <div class="slider-green mx-3">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="vertical"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>
                    <div class="slider-yellow mx-3">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="vertical"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>
                    <div class="slider-teal mx-3">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="vertical"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>
                    <div class="slider-purple mx-3">
                      <input type="text" value="" class="slider form-control" data-bs-slider-min="-200" data-bs-slider-max="200"
                           data-bs-slider-step="5" data-bs-slider-value="[-100,100]" data-bs-slider-orientation="vertical"
                           data-bs-slider-selection="before" data-bs-slider-tooltip="show">
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
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
      /* BOOTSTRAP SLIDER */
      $('.slider').bootstrapSlider()

      /* ION SLIDER */
      $('#range_1').ionRangeSlider({
        min     : 0,
        max     : 5000,
        from    : 1000,
        to      : 4000,
        type    : 'double',
        step    : 1,
        prefix  : '$',
        prettify: false,
        hasGrid : true
      })
      $('#range_2').ionRangeSlider()

      $('#range_5').ionRangeSlider({
        min     : 0,
        max     : 10,
        type    : 'single',
        step    : 0.1,
        postfix : ' mm',
        prettify: false,
        hasGrid : true
      })
      $('#range_6').ionRangeSlider({
        min     : -50,
        max     : 50,
        from    : 0,
        type    : 'single',
        step    : 1,
        postfix : '°',
        prettify: false,
        hasGrid : true
      })

      $('#range_4').ionRangeSlider({
        type      : 'single',
        step      : 100,
        postfix   : ' light years',
        from      : 55000,
        hideMinMax: true,
        hideFromTo: false
      })
      $('#range_3').ionRangeSlider({
        type    : 'double',
        postfix : ' miles',
        step    : 10000,
        from    : 25000000,
        to      : 35000000,
        onChange: function (obj) {
          var t = ''
          for (var prop in obj) {
            t += prop + ': ' + obj[prop] + '\r\n'
          }
          $('#result').html(t)
        },
        onLoad  : function (obj) {
          //
        }
      })
    })
  </script>

