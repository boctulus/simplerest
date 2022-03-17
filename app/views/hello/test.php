
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
              
    <table class="table table-striped thead-default table-bordered" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; border: 1px solid #e2e8f0;">
  <thead>
    <tr>
      <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Col 1</th>
      <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Col 2</th>
      <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Col 3</th>
      <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Col 4</th>
    </tr>
  </thead>
  <tbody>
    <tr style="" bgcolor="#f2f2f2">
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 1</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 2</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 3</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 4</td>
    </tr>
    <tr>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 1</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 2</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 3</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 4</td>
    </tr>
    <tr class="table-success" style="" bgcolor="#f2f2f2">
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" bgcolor="#84e8ba" valign="top">Col Data 1</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" bgcolor="#84e8ba" valign="top">Col Data 2</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" bgcolor="#84e8ba" valign="top">Col Data 3</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" bgcolor="#84e8ba" valign="top">Col Data 4</td>
    </tr>
    <tr>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 1</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 2</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 3</td>
      <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="left" valign="top">Col Data 4</td>
    </tr>
  </tbody>
</table>

      <?php

      // echo tag('table')
      // ->rows([
      //   'Item',
      //   'Valor',
      //   'Moneda'
      // ])
      // ->cols([
      //   ['Valor de la compra en dólares', '$ 3000', 'USD'],
      //   ['Tarifa a cobrar por kilo', '$ 400', 'USD'],
      //   ['Kilos bruto', 2, 'Kg'],
      //   ['Flete aéreo (Miami-Santiago)', 0, 'USD'],
      //   ['Seguro (2%)',  0, 'USD'],
      //   ['Valor C.I.F',  0, 'USD'],
      //   ['Derechos (6%)', 0, 'USD'],
      //   ['Valor neto', 0, 'USD'],
      //   ['IVA', 0, 'USD'],
      //   ['Valor final', 0, 'USD'],
      //   ['TTL US$', 0, 'USD']
      // ])
      // ->color('light')
      // ->headOptions([
      //   'color' => 'dark'
      // ])
      // ->colorCol([
      //   'pos'   => 1, 
      //   'color' => 'primary'
      // ])
      // ;

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

