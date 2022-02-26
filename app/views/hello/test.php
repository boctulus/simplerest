<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>


<div class="container mt-5">

  <?php


    echo tag('div')->content('Some content')->textColor('primary')->class('my-3');    
    echo tag('div')->content('Some content but with opacity of 50%')->textColor('primary')->opacity(0.5);

    /* Badges */    

    // //echo tag('badge')->content('barato')->class('mb-3 me-3 rounded-pill')->danger();

    // echo tag('badge')->content('barato')->class('mb-3 me-3 rounded-pill')->color('warning');

    // echo tag('badge')->content('barato')->class('mb-3 me-3 rounded-pill')->bg('success'); // ok

    // echo tag('button')->content([
    //     'Inbox',
    //     tag('badge')->content('99+')->bg('danger')->class('position-absolute top-0 start-100 translate-middle rounded-pill')
    // ])
    // ->class('rounded position-relative')
    // ->primary();

  ?>
   

</div>


<script>

</script>