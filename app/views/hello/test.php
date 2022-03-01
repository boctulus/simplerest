<?php

use simplerest\core\libs\HtmlBuilder\AdminLte;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


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

      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-ban"></i> Alert!</h5>
        Danger alert preview. This alert is dismissable. A wonderful serenity has taken possession of my
        entire
        soul, like these sweet mornings of spring which I enjoy with my whole heart.
      </div>
      
    <?php

        

        // echo tag('alert')->content(
        //     tag('alertLink')->href('#')->anchor('A danger content')
        // )->color('danger')->dismissible(true);
        
       

    ?>

    


      <?php
      //echo tag('file')->multiple();

      ?>

    </div>
  </div>