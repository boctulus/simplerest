<?php

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use simplerest\core\libs\i18n\Translate;

/*
    Exporta a .po y .mo todos arrays de traducciones al subfolder LC_MESSAGES dentro
    de cada folder de lenguaje.
*/
function exportLangDef(bool $include_mo = true, string $locale_path = null)
{   
    Translate::xportLangDef($include_mo, $locale_path);
}
  
/*
    https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
*/
function setLang(?string $lang){
    Translate::setLang($lang);
}

function trans(string $text){
    return Translate::trans($text);
}