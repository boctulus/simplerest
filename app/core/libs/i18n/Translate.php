<?php

namespace simplerest\core\libs\i18n;

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use simplerest\core\Container;
use simplerest\core\libs\Files;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Strings;
use simplerest\core\libs\i18n\POParser;

/*
    Ver mejores soluciones que la clase POParse que estoy usando como:

    https://github.com/php-gettext/Gettext
    https://github.com/pherrymason/PHP-po-parser

    Más
    https://stackoverflow.com/a/16744070/980631
*/

class Translate
{
    static $currentTextDomain = null;
    static $domainPaths       = [];
    static $translations      = [];
    static $currentLang       = null;

    static $useGettext        = null;

    /*
        Si debe usarse gettext() o por el contrario un reemplazo
    */
    static function useGettext(bool $val = true){
        static::$useGettext = $val;
    }

    static function setLocale(string $lang, string $encoding = 'UTF-8')
    {    
        static::$currentLang = $lang;

        if (static::$useGettext){
            setlocale(LC_ALL, "$lang.$encoding");  
            putenv("LANGUAGE=$lang.$encoding");
        }
    }

    static function getLocale(){
        return static::$currentLang;
    }
    
    static function bind(string $domain, $path = LOCALE_PATH){
        static::$currentTextDomain    = $domain;
        static::$domainPaths[$domain] = $path;

        if (static::$useGettext){
            bindtextdomain($domain, $path);
        } else {
            Container::singleton('po_parser', POParser::class);

            static::$translations[$domain] = Container::make('po_parser')
            ->parse(LOCALE_PATH .  static::$currentLang . "/LC_MESSAGES/$domain.po");
        }

        // De una vez selecciono el text domain
        return static::setTextDomain($domain);
    }

    static function setTextDomain(string $domain) : bool {
        if (!isset(static::$domainPaths[$domain])){
            throw new \InvalidArgumentException("Domain '$domain' was not binded");
        }

        $ret = textdomain($domain);

        if ($ret != $domain){
            Files::logger("Error trying to set text domain to '$domain'");
            return false;
        }

        return true;
    }

    static function gettext(string $text){
        if (static::$useGettext){
            return gettext($text);
        }

        $translation = static::$translations[static::$currentTextDomain][$text] ?? $text;

        return !empty($translation) ? $translation : $text;
    }

    // alias de Translate::gettext()
    static function trans(string $text){
        return static::gettext($text);
    }

    /*
        https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
    */
    static function setLang(?string $lang){
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
        static::setLocale($selected, $encode);
    }

    /*
        Exporta a .po y .mo todos arrays de traducciones al subfolder LC_MESSAGES dentro
        de cada folder de lenguaje.
    */
    static function exportLangDef(bool $include_mo = true, string $locale_path = null)
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
}
