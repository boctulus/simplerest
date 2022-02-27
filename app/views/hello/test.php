<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<style>
  
</style>

  <?php

  echo tag('table')
    ->rows([
      '#',
      'First',
      'Last',
      'Handle'
    ])
    ->cols([
      [
        1,
        'Mark',
        'Otto',
        '@mmd'
      ],
      [
        2,
        'Lara',
        'Cruz',
        '@fat'
      ],
      [
        3,
        'Juan',
        'Cruz',
        '@fat'
      ],
      [
        4,
        'Feli',
        'Bozzolo',
        '@facebook'
      ]
    ])
    ->color('light')
    ->headOptions([
      'color' => 'dark'
    ]);

  ?>

</div>


<script>

</script>