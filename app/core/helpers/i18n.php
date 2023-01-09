<?php

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use simplerest\core\libs\i18n\Translate;
  
/*
    https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
*/
function setLang(?string $lang){
    Translate::setLang($lang);
}

function trans(string $text){
    return Translate::trans($text);
}