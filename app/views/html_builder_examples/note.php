<h3>Note</h3>

<?php

use simplerest\core\libs\HtmlBuilder\Tag;

Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

// $html = tag('note')
// ->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
// elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
// necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
// soluta porro?')
// ->color('secondary')->class('mb-5');


$html = tag('note', [ // Pasa el segundo argumento como un array
    'color' => 'secondary',
    'class' => 'mb-5'
])->text('<strong>!!! Note secondary:</strong> Lorem, ipsum dolor sit amet consectetur adipisicing
elit. Cum doloremque officia laboriosam. Itaque ex obcaecati architecto! Qui
necessitatibus delectus placeat illo rem id nisi consequatur esse, sint perspiciatis
soluta porro?');

echo $html;
