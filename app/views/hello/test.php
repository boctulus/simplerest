<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

?>

<style>



</style>

<div class="row mt-5">
  <div class="col-6 offset-3">

    <?php
      
      echo tag('shadow')
      ->content('Some content')
      ->class("p-3 shadow-lg");

    ?>

  </div>
</div>

<?php

//Bt5Form::setIdAsName();

// echo tag('div')->class('mt-5 p-3 py-5 mb-2')
// ->content('')
// ->textColor('white')->bg('success');

// echo tag('div')->class('mt-5 p-3 py-5 mb-2')
// ->content('Gradiente?')
// ->textColor('white')->bg('success')->gradient();


?>