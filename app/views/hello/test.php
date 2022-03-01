<?php

use simplerest\core\libs\HtmlBuilder\AdminLte;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


//Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\AdminLte::class);

include_css(ASSETS_PATH . 'adminlte/css/adminlte.css');

?>

<style>
  /* .color-palette {
      height: 35px;
      line-height: 35px;
      text-align: right;
      padding-right: .75rem;
    }

    .color-palette.disabled {
      text-align: center;
      padding-right: 0;
      display: block;
    }

    .color-palette-set {
      margin-bottom: 15px;
    }

    .color-palette span {
      display: none;
      font-size: 12px;
    }

    .color-palette:hover span {
      display: block;
    }

    .color-palette.disabled span {
      display: block;
      text-align: left;
      padding-left: .75rem;
    }

    .color-palette-box h4 {
      position: absolute;
      left: 1.25rem;
      margin-top: .75rem;
      color: rgba(255, 255, 255, 0.8);
      font-size: 12px;
      display: block;
      z-index: 7;
    } */
</style>

<div class="row mt-5">
  <div class="col-6 offset-3">


    <?php
        /*
            Previous | Ene | Feb | Mar | Next
        */
        echo tag('paginator')->content([
          [
            'href'   => '#?page=1',
            'anchor' => '<p class="page-month">Ene</p>
                        <p class="page-year">2021</p>'
          ],
          [
              'href'   => '#?page=2',
              'anchor' => '<p class="page-month">Feb</p>
                          <p class="page-year">2021</p>',
              'active' => true
          ],
          [
            'href'   => '#?page=3',
            'anchor' => '<p class="page-month">Mar</p>
                        <p class="page-year">2021</p>'
          ],
          // ...
      ])
      ->class('mt-3 pagination-month')
      //->large()
      ->options(['justify-content-center'])
      ->withPrev([
          'href'   => '#?page=1',
          'anchor' => '&laquo;',
          //'disabled' => true
      ])
      ->withNext([
          'href'   => '#?page=11',
          'anchor' => '&raquo;',
          //'disabled' => true
      ])
      ;
          

    ?>


  </div>
</div>