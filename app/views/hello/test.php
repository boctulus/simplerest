
<!-- datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<!-- https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js -->
<script src="http://simplerest.lan:8082/public/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>


<?php

use simplerest\core\libs\HtmlBuilder\AdminLte;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
//Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\AdminLte::class);

include_css(ASSETS_PATH . 'adminlte/dist/css/adminlte.css');


?>
<style>
  
</style>


<div class="row">
    <div class="col-6 mt-3 offset-3">
              
      <?php

      echo tag('table')
      ->rows([
      '#',
      'First',
      'Last',
      'Handle'
      ])
      ->cols([
      [
          1,
          'Mark',
          'Otto',
          '@mmd'
      ],
      [
          2,
          'Lara',
          'Cruz',
          '@fat'
      ],
      [
          3,
          'Juan',
          'Cruz',
          '@fat'
      ],
      [
          4,
          'Feli',
          'Bozzolo',
          '@facebook'
      ]
      ])
      ->color('light')
      ->headOptions([
        'color' => 'dark'
      ])
      ->colorCol([
        'pos'   => 1, 
        'color' => 'primary'
      ])
      ;

      ?>
  

    </div>
</div>

<script>
  // $(function(){
  //   $('#reservationdatetime').datepicker();

  //   //Date range picker
  //   $('#reservation').daterangepicker();
  // });
</script>

