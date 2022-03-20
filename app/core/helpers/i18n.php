<?php

use simplerest\core\libs\Strings;
use simplerest\core\libs\Files;
use simplerest\core\libs\StdOut;

/*
    Exporta a .po todos arrays de traducciones al subfolder LC_MESSAGES dentro
    de cada folder de lenguaje.
*/
function exportLangDef()
{   
    foreach (new \DirectoryIterator(LOCALE_PATH) as $fileInfo) {
        if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
        
        $dir = $fileInfo->getBasename();
        //d($dir);

        foreach (new \DirectoryIterator(LOCALE_PATH . "/$dir") as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->isDir()) continue;

            if ($fileInfo->getExtension() != 'php'){
                continue;
            }

            $def_file = $fileInfo->getBasename();
            $domain   = Strings::beforeLast($def_file, '.');

            include LOCALE_PATH . "$dir/" . $def_file;

            Files::mkdir(LOCALE_PATH . "$dir/" . "LC_MESSAGES");

            $po_path = LOCALE_PATH . "$dir/" . "LC_MESSAGES/$domain.po";

            $fp = fopen($po_path, 'w');
            StdOut::pprint("Generating $po_path");

            fwrite($fp, "#\n");
            fwrite($fp, "msgid \"\"\n");
            fwrite($fp,  "msgstr \"\"\n");

            foreach ($intl as $key => $value){
                if (empty($key)){
                    continue;
                }

                $key   = addslashes($key);
                $value = addslashes($value);

                fwrite($fp, "\n");
                fwrite($fp, "msgid \"$key\"\n");
                fwrite($fp, "msgstr \"$value\"\n");
            }

            fclose($fp);
        }
    } 
}
  
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