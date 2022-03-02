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
    
    <!-- <a class="btn btn-app">
      <i class="fas fa-edit"></i> Edit
    </a>
    <a class="btn btn-app">
      <i class="fas fa-play"></i> Play
    </a>
    <a class="btn btn-app">
      <i class="fas fa-pause"></i> Pause
    </a>
    <a class="btn btn-app">
      <i class="fas fa-save"></i> Save
    </a>
  -->

    <a class="btn btn-app">
      <span class="badge bg-warning">3</span>
      <i class="fas fa-bullhorn"></i> Notifications
    </a>

    <!--
    <a class="btn btn-app">
      <span class="badge bg-success">300</span>
      <i class="fas fa-barcode"></i> Products
    </a>
    <a class="btn btn-app">
      <span class="badge bg-purple">891</span>
      <i class="fas fa-users"></i> Users
    </a>
    <a class="btn btn-app">
      <span class="badge bg-teal">67</span>
      <i class="fas fa-inbox"></i> Orders
    </a>
    <a class="btn btn-app">
      <span class="badge bg-info">12</span>
      <i class="fas fa-envelope"></i> Inbox
    </a>
    <a class="btn btn-app">
      <span class="badge bg-danger">531</span>
      <i class="fas fa-heart"></i> Likes
    </a> -->

    <?php

        echo tag('appButton')
        ->content("Edit")
        ->icon('edit')
        ->href('#edit')
        ->bg('danger')
        ->badgeQty(5)
        ->badgeColor('warning')
        ; 

    ?>


  </div>
</div>