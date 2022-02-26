<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>


<div class="container mt-5">

    
  <?php

    echo tag('card')->style('width: 18rem;')->class('my-3')
    // ->content(
    //   tag('cardImgTop')->src(asset('img/mail.png'))
    // )
    ->body([   
      tag('cardTitle')->text('Some title'),        
      tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
      tag('inputButton')->value('Go somewhere')
    ]);


      echo tag('card')->style('width: 18rem;')->class('my-3')
    // ->content(
    //   tag('cardImgTop')->src(asset('img/mail.png'))
    // )
    ->header(tag('cardTitle')->text('Some title'))
    ->body([           
      tag('cardText')->text('Some quick example text to build on the card title and make up the bulk of the cards content.'),
      tag('inputButton')->value('Go somewhere')
    ]);

  ?>
   

</div>


<script>

</script>