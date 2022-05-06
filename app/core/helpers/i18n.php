<?php

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use simplerest\core\libs\Strings;
use simplerest\core\libs\Files;
use simplerest\core\libs\StdOut;

/*
    Exporta a .po y .mo todos arrays de traducciones al subfolder LC_MESSAGES dentro
    de cada folder de lenguaje.
*/
function exportLangDef(bool $include_mo = true, string $locale_path = null)
{   
    if ($locale_path === null){
        $locale_path = LOCALE_PATH;
    }

    //d($locale_path);

    foreach (new \DirectoryIterator($locale_path) as $fileInfo) {
        if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
        
        $dir = $fileInfo->getBasename();
        //StdOut::pprint($dir);

        foreach (new \DirectoryIterator($locale_path . "/$dir") as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->isDir()) continue;

            if ($fileInfo->getExtension() != 'php'){
                continue;
            }

            $def_file = $fileInfo->getBasename();
            $domain   = Strings::beforeLast($def_file, '.');

            //StdOut::pprint($def_file);

            include $locale_path . "$dir/" . $def_file;

            Files::mkdir($locale_path . "$dir/" . "LC_MESSAGES");

            $po_path = $locale_path . "$dir/" . "LC_MESSAGES/$domain.po";
            $mo_path = $locale_path . "$dir/" . "LC_MESSAGES/$domain.mo";

            $fp = fopen($po_path, 'w');
            StdOut::pprint("Generating $po_path");

            $header = <<<HEADER
            msgstr\t"Project-Id-Version: SimpleRest Translations 0.0.1\\n"
            \t"Report-Msgid-Bugs-To: boctulus@gmail.com\\n"
            \t"POT-Creation-Date: 2010-05-28 06:18-0500\\n"
            \t"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"
            \t"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"
            \t"Language-Team: LANGUAGE <boctulus@gmail.com\\n"
            \t"MIME-Version: 1.0\\n"
            \t"Content-Type: text/plain; charset=UTF-8\\n"
            \t"Content-Transfer-Encoding: 8bit\\n"
            HEADER;

            fwrite($fp, "#\n");
            fwrite($fp, "msgid \"\"\n");
            fwrite($fp,  "$header\n");

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

            if ($include_mo){
                StdOut::pprint("Compiling to $mo_path");
                $exit_code = (int) shell_exec("msgfmt $po_path -o $mo_path; echo $?");
                StdOut::pprint("Compilation to $mo_path " . ($exit_code === 0 ? '-- ok' : '-- error'));
                StdOut::pprint('');
            }
            
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

    //StdOut::pprint("Setting locale to '$selected.$encode'");

    setlocale(LC_ALL, "$selected.$encode");  
    putenv("LANGUAGE=$selected.$encode");
}