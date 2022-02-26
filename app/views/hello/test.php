<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>


<div class="container mt-5">

  <div class="w-25 p-3" style="background-color: #eee;">Width 25%</div>
  <div class="w-50 p-3" style="background-color: #eee;">Width 50%</div>
  <?php
    echo tag('div')->content(
      'Width 75%'
    )
    ->w(75)
    ->bg('warning')
    ->class('p-3');

  ?>
  <div class="w-100 p-3" style="background-color: #eee;">Width 100%</div>
  <?php
    echo tag('div')->content(
      'Width auto'
    )
    ->w('auto')
    ->bg('warning')
    ->class('p-3');

  ?>
    
  <div style="height: 100px; background-color: rgba(255,0,0,0.1);">
    <div class="h-25 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 25%</div>
    <div class="h-50 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 50%</div>

    <?php
      echo tag('div')->content(
        'Height 75%'
      )
      ->w(75)
      ->h(75)
      ->bg('warning')
      ->class('d-inline-block');

    ?>

    <div class="h-100 d-inline-block" style="width: 120px; background-color: rgba(0,0,255,.1)">Height 100%</div>
    <?php
      echo tag('div')->content(
        'Height auto'
      )
      ->w(75)
      ->h('auto')
      ->bg('danger')
      ->class('d-inline-block');

    ?>
  </div>    

  <?php

  ?>
   

</div>


<script>

</script>