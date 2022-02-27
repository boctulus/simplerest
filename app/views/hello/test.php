<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;


Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

//Bt5Form::setIdAsName();

?>

<div class="form-group">
  <div class="input-group input-group-lg">
    <input type="search" class="form-control form-control-lg" placeholder="Type your keywords here" value="Lorem ipsum">
    <div class="input-group-append">
      <button type="submit" class="btn btn-lg btn-default">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </div>
</div>