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


    <table class="table table-light">
      <thead class="table-dark">
        <tr>
          <th scope="row">Item</th>
          <th scope="row" class="table-primary">Valor</th>
          <th scope="row">Moneda</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">Valor de la compra en dólares</th>
          <td class="table-primary">$ 3000</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Tarifa a cobrar por kilo</th>
          <td class="table-primary">$ 400</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Kilos bruto</th>
          <td class="table-primary">2</td>
          <td>Kg</td>
        </tr>
        <tr>
          <th scope="row">Flete aéreo (Miami-Santiago)</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Seguro (2%)</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Valor C.I.F</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Derechos (6%)</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Valor neto</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">IVA</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">Valor final</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>
        <tr>
          <th scope="row">TTL US$</th>
          <td class="table-primary">0</td>
          <td>USD</td>
        </tr>

        <tr>
          <td style="background-color: #ffffff;"></td>
          <td style="background-color: #ffffff;"></td>
          <td style="background-color: #ffffff;"></td>
        </tr>

        <tr>
          <td scope="row" style="font-weight: 600;">TOTAL</td>
          <td class="table-primary">$ 3000</td>
          <td>USD</td>
        </tr>
      </tbody>
    </table>


  </div>
</div>

<script>
  // $(function(){
  //   $('#reservationdatetime').datepicker();

  //   //Date range picker
  //   $('#reservation').daterangepicker();
  // });
</script>