<?php

use simplerest\libs\Strings;
  
/*
    https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
*/
function setLang(?string $lang){
    static $langs = [];
    static $pure_langs = [];

    $selected = null;

    if ($lang === NULL || $lang === '*'){
        return;
    }
    
    $encode = "UTF-8";
    $throw  = false;

    if (empty($langs)){
        foreach (new \DirectoryIterator(LOCALE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $filename = $fileInfo->getBasename();
            $langs[]  = $filename;
            $pure_langs[] = substr($filename, 0, 2);
        } 
    }

    /*
        If there is no translation for an specific country then to use any for that language
    */
    if (!Strings::contains('_', $lang)){
        $only_lang = substr($lang, 0, 2);
        $lang_ix   = array_search($only_lang, $pure_langs);
        $selected  = $langs[$lang_ix];     
    } else {
        // full format
        $lang_ix   = array_search($lang, $langs);

        if ($lang_ix !== false){
            $selected  = $langs[$lang_ix];    
        }
    }

    /*
        If every thing fails, last chance
    */
    if ($selected === null){
        $only_lang = substr($lang, 0, 2);
        $lang_ix   = array_search($only_lang, $pure_langs);
        $selected  = $langs[$lang_ix];     
    }

    if ($selected === null){
        if ($throw){
            throw new \InvalidArgumentException("Invalid lang $lang");
        }

        return;        
    }

    setlocale(LC_ALL, "$selected.$encode");  
}