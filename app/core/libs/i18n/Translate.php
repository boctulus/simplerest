<?php

namespace simplerest\core\libs\i18n;

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use simplerest\core\Container;
use simplerest\core\libs\Files;
use simplerest\core\libs\HtmlBuilder\Tag;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\core\libs\i18n\POParser;
use simplerest\core\libs\System;

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
        Define si debe usarse gettext() o por el contrario un reemplazo
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
            putenv("LC_ALL=$lang"); // Windows
        }
    }

    static function getLocale(){
        return static::$currentLang;
    }

    static function getDomain(){
        return static::$currentTextDomain;
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
            Logger::logError("Error trying to set text domain to '$domain'");
            return false;
        }

        return true;
    }

    static function gettext(string $text, $text_domain = null){
        if ($text_domain !== null){
            static::bind($text_domain);
            static::$currentTextDomain = $text_domain;
        }

        if (static::$useGettext){
            return gettext($text);
        }

        $text_domain = $text_domain ?? static::$currentTextDomain;

        $translation = static::$translations[$text_domain][$text] ?? $text;

        return !empty($translation) ? $translation : $text;
    }

    // alias de Translate::gettext()
    static function trans(string $text, $text_domain = null){
        return static::gettext($text, $text_domain);
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
        Exporta a archivos .po y .mo todos arrays de traducciones de lenguaje.

        @param bool   $include_mo  dermina si debe generarse el archivo .mo
        @param string $locale_path si se especifica lo toma como directorio de entrada (para los .pot.php) y de salida sino se especificara
        @param string $to si se especifca se toma como directorio de salida para generacion de archivos .php y .po / .mo
        @param string $preset es un nombre para una determinada forma de hacer el scaffolding
    */
    static function exportLangDef(bool $include_mo = true, string $locale_path = null, string $to = null, string $text_domain = null, string $preset = null)
    {   
        if ($locale_path === null){
            $locale_path = LOCALE_PATH;
        }

        $to = $to ?? $locale_path;
        $to = rtrim($to, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // dd($locale_path);

        foreach (new \DirectoryIterator($locale_path) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $lang = $fileInfo->getBasename();
            // StdOut::pprint($lang);
            // continue; //

            foreach (new \DirectoryIterator($locale_path . "/$lang") as $fileInfo) {
                if($fileInfo->isDot() || $fileInfo->isDir()) continue;

                if ($fileInfo->getExtension() != 'php'){
                    continue;
                }

                $def_file = $fileInfo->getBasename();
                $domain   = Strings::beforeLast($def_file, '.');

                if ($text_domain != null && $domain != $text_domain){
                    continue;
                }

                //StdOut::pprint($def_file);

                $intl = include $locale_path . "$lang/" . $def_file;

                switch ($preset){
                    case "wp":
                        $path_no_ext = "$to{$domain}-$lang";

                        $po_path     = "$path_no_ext.po";
                        $mo_path     = "$path_no_ext.mo";
                        break;
                    default:
                        Files::mkdir($to . "$lang/" . "LC_MESSAGES");

                        $po_path = $to   . "$lang/" . "LC_MESSAGES/$domain.po";
                        $mo_path = $to   . "$lang/" . "LC_MESSAGES/$domain.mo";
                }                

                $po_path = Files::normalize($po_path);

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

                    $value = str_replace('?', '%s', $value);

                    fwrite($fp, "\n");
                    fwrite($fp, "msgid \"$key\"\n");
                    fwrite($fp, "msgstr \"$value\"\n");
                }

                fclose($fp);    

                if ($include_mo){
                    StdOut::pprint("Compiling to $mo_path");

                    exec("msgfmt $po_path -o $mo_path", $output, $exit_code);
                    
                    StdOut::pprint("Compilation to $mo_path " . ($exit_code === 0 ? '-- ok' : '-- error'));
                    StdOut::pprint('');
                }
                
            }
        } 
    }

    static function convertPot(string $locale_path = null, string $text_domain = null){
        if ($locale_path === null){
            $locale_path = LOCALE_PATH;
        }

        if ($text_domain !== null){
            $php_pot = [ $locale_path . "$text_domain.pot.php" ];

            if (!file_exists($php_pot[0])){
                StdOut::pprint("File '$text_domain.pot.php' not found");
                exit;
            }
        } else {
            $php_pot = glob($locale_path . '*.pot.php');
        }

        if (empty($php_pot)){
            StdOut::pprint("No *.pot.php files found");
            exit;
        }
        
        foreach ($php_pot as $pp){
            $pot_domain = Strings::beforeIfContains(basename($pp), '.pot.php');
            
            // dd($pot_domain, 'DOMAIN');

            foreach (new \DirectoryIterator($locale_path) as $fileInfo) {
                if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
                
                $dir = $fileInfo->getBasename();
                
                // dd($dir, 'DIR');
                // StdOut::pprint($dir);
    
                $path = $locale_path . DIRECTORY_SEPARATOR . $dir;
                foreach (new \DirectoryIterator($path) as $fileInfo) {
                    if($fileInfo->isDot()){
                        continue;
                    } 

                    // dd($dir, 'DIR');   
    
                    $filename = "$path/$pot_domain.php";

                    // dd(file_exists($filename), $filename);
            
                    $defs = include $pp;

                    // dd($defs, $filename   );

                    if (!file_exists($filename)){
                        StdOut::pprint("Creando archivo $pot_domain.php en $dir");

                        $p_defs = [];
                        foreach ($defs as $def){
                            $clean_def = str_replace([
                                ':/f/', 
                                ':/m/'
                            ], '', trim($def));

                            $p_defs[$def] = ($dir == 'en_US') ? $clean_def : "";
                        }

                        Files::varExport($p_defs, $filename);
                    } else {
                        $current_defs      = include $filename;

                        // dd($current_defs, "DEFS de $filename");

                        $current_def_keys  = array_keys($current_defs);

                        $not_incl_def_keys = array_diff($defs, $current_def_keys);

                        if (!empty($not_incl_def_keys)){
                            foreach ($not_incl_def_keys as $def){
                                $current_defs[$def] = "";
                            }
                            
                            // dd($current_defs, $dir);

                            StdOut::pprint("Actualizando $filename");
                            Files::varExport($current_defs, $filename);
                        }
                    }

                   
                }
            }

        }

    }
    
}

