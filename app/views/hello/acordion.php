<?php

use simplerest\core\libs\Bt5Form; 
use simplerest\core\libs\Tag;


Tag::registerBuilder(\simplerest\core\libs\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class = "row mt-5">
    <div class = "col-6 offset-3">
    <?php 

        // si escribo mal el nombre del tag se rompe feo
        echo tag('accordion')->items([
            [
                'id' => "flush-collapseOne",
                'title' => "Accordion Item #1",
                'body' => 'Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.'
            ],
            [
                'id' => "flush-collapseTwo",
                'title' => "Accordion Item #2",
                'body' => 'Placeholder 2'
            ],
            [
                'id' => "flush-collapseThree",
                'title' => "Accordion Item #3",
                'body' =>  'Placeholder 3'
            ]
        ])
        ->id('accordionExample')
        ->always_open(true)
        ->attributes(['class' => 'accordion-flush'])
        ;

    ?>
    </div>
</div>