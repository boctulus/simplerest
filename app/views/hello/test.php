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
    <div class="col-6 offset-3"
          
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Date picker</h3>
              </div>
              <div class="card-body">
                <!-- Date -->
                <div class="form-group">
                  <label>Date:</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                        <div class="input-group-append" data-target="#reservationdate" data-bs-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <!-- Date and time -->
                <div class="form-group">
                  <label>Date and time:</label>
                    <div class="input-group date" id="reservationdatetime" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime"/>
                        <div class="input-group-append" data-target="#reservationdatetime" data-bs-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <!-- /.form group -->
                <!-- Date range -->
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
                <!-- /.form group -->

                <!-- Date and time range -->
                <div class="form-group">
                  <label>Date and time range:</label>

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" class="form-control float-right" id="reservationtime">
                  </div>
                  <!-- /.input group -->
                </div>
                <!-- /.form group -->

                <!-- Date and time range -->
                <div class="form-group">
                  <label>Date range button:</label>

                  <div class="input-group">
                    <button type="button" class="btn btn-default float-right" id="daterange-btn">
                      <i class="far fa-calendar-alt"></i> Date range picker
                      <i class="fas fa-caret-down"></i>
                    </button>
                  </div>
                </div>
                <!-- /.form group -->
              </div>
                <div class="card-footer">
                  Visit <a href="https://getdatepicker.com/5-4/">tempusdominus </a> for more examples and information about
                  the plugin.
                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


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
    
    //$('#comidas_duallistbox').bootstrapDualListbox()

    $(function () {
      $( '.select2' ).select2( {
          theme: 'bootstrap-5'
      } );

      $('.duallistbox').bootstrapDualListbox()

       //Datemask dd/mm/yyyy
      $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })

      //Datemask2 mm/dd/yyyy
      
      $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
      
      //Money Euro
      $('[data-mask]').inputmask()

    //Date picker
    $('#reservationdate').datetimepicker({
            format: 'L'
    });

    //Date and time picker
    //$('#reservationdatetime').f({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })


    })
  </script>

