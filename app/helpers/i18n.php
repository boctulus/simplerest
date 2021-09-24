<?php

use simplerest\libs\Strings;
  
/*
    https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
*/
function setLang(?string $lang){
    static $_langs = [];

    if ($lang === NULL || $lang === '*'){
        return;
    }
    
    $encode = "UTF-8";
    $throw  = false;

    if (empty($_langs)){
        foreach (new \DirectoryIterator(LOCALE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $filename = $fileInfo->getBasename();
            $_langs[] = $filename;
        } 
    }

    /*
        If there is no translation for an specific country then to use any for that language
    */

    $found = false;
    if (!Strings::contains('_', $lang)){
        foreach ($_langs as $l){
            if (substr($l, 0, 2) == $lang){
                $found = true;                
                $lang  = $l;
                break;
            }
        }      
    }

    if (!$found && $throw){
        throw new \Exception("Invalid lang $lang");
    }

    setlocale(LC_ALL, "$lang.$encode");  
}