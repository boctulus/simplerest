<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>


<div class="container mt-5">

    
  <?php

  echo tag('link')
  ->href("www.solucionbinaria.com")
  ->anchor('SolucionBinaria .com')
  ->textColor('success')
  ->class('mb-3');

  echo tag('p');
  
  echo tag('link')
  ->href("www.solucionbinaria.com")
  ->anchor('SolucionBinaria .com')
  ->color('success')
  ->class('mb-3');


  ?>
   

</div>


<script>

</script>