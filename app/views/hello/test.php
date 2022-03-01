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

  <!-- <div class="callout callout-danger">
    <h5>I am a danger callout!</h5>

    <p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire
      soul,
      like these sweet mornings of spring which I enjoy with my whole heart.</p>
  </div> -->
  
  <?php
      echo tag('progress')->content(
          tag('progressBar')->current(80)
      )->class('mt-5');

      echo tag('progress')->content(
          tag('progressBar')
          ->min(5)
          ->max(25)
          ->current(15)->withLabel()->striped()
      )->class('my-5');

      echo tag('progress')->content(
          tag('progressBar')
          ->current(25)->withLabel()->bg('danger')->animated()
      )->class('my-5')->style("height: 50px;");

      // progress-xxs
      echo tag('progress')->content(
        tag('progressBar')
        ->current(25)->bg('danger')->animated()
      )->class('my-5')
      ->size('sm');  


      echo tag('progress')->content([
          tag('progressBar')
          ->current(15)->withLabel()->bg('primary'),

          tag('progressBar')
          ->current(30)->withLabel()->bg('success'),

          tag('progressBar')
          ->current(25)->withLabel()->bg('info')
      ])->class('mt-3');

  ?>


  </div>
</div>