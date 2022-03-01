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

  <div id="robotcarousel" class="carousel slide" data-bs-ride="carousel">
  
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#robotcarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#robotcarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
  </div>
  
  <div class="carousel-inner">
    <div class="carousel-item">
      <img class="d-block w-100" src="https://pixelprowess.com/i/carousel_swamp.png" alt="swamp">
    </div>
    <div class="carousel-item active">
      <img class="d-block w-100" src="https://pixelprowess.com/i/carousel_flight.png" alt="flight">
    </div>
  </div>
  
  <button class="carousel-control-prev" type="button" data-bs-target="#robotcarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#robotcarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
  
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