<?php

namespace simplerest\core\libs\i18n;

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use simplerest\core\Container;
use simplerest\core\libs\Files;
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

    https://claude.ai/chat/0234fab7-c622-46d2-8b27-8ed4a58f0654
*/


class Translate
{
    static $currentTextDomain = null;
    static $domainPaths       = [];
    static $translations      = [];
    static $currentLang       = null;
    static $useGettext        = null;
    static $ext_loaded;
    static $fallbackLocale    = 'en_US';
    static $pluralForms       = [];

    static function checkGetTextLoaded(bool $log_error = true){
        if (static::$ext_loaded !== null){
            return static::$ext_loaded;
        } 

        static::$ext_loaded = function_exists('gettext');

        if (!static::$ext_loaded && $log_error){
            $transient_key = 'gettext_extension_error_logged';
            
            if (!get_transient($transient_key)) {
                Logger::logError('Extension GetText not loaded');
                set_transient($transient_key, true, 3600 * 24);
            }
        }
        
        return static::$ext_loaded;
    }

    static function useGettext(bool $val = true){
        static::$useGettext = $val && static::checkGetTextLoaded(false);
    }

    static function setLocale(string $lang, string $encoding = 'UTF-8')
    {    
        static::$currentLang = $lang;

        if (static::$useGettext){
            setlocale(LC_ALL, "$lang.$encoding");  
            putenv("LANGUAGE=$lang.$encoding");
            putenv("LC_ALL=$lang"); 
        }
    }

    static function getLocale(){
        return static::$currentLang;
    }

    static function getDomain(){
        return static::$currentTextDomain;
    }

    // Nuevo: Interpolación de variables
    protected static function interpolate(string $message, array $context = []): string 
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }
    
    static function bind(string $domain, $path = LOCALE_PATH){
        static::$currentTextDomain    = $domain;
        static::$domainPaths[$domain] = $path;

        if (static::$useGettext){
            bindtextdomain($domain, $path);
        } else {
            Container::singleton('po_parser', POParser::class);

            static::$translations[$domain] = Container::make('po_parser')
                ->parse(LOCALE_PATH . static::$currentLang . "/LC_MESSAGES/$domain.po");
        }

        return static::setTextDomain($domain);
    }

    static function setTextDomain(string $domain) : bool {
        if (!isset(static::$domainPaths[$domain])){
            throw new \InvalidArgumentException("Domain '$domain' was not binded");
        }

        if (static::$useGettext){
            $ret = \textdomain($domain);

            if ($ret != $domain){
                Logger::logError("Error trying to set text domain to '$domain'");
                return false;
            }
        }

        return true;
    }

    static function gettext(string $text, $text_domain = null){
        if ($text_domain !== null){
            static::bind($text_domain);
            static::$currentTextDomain = $text_domain;
        }

        if (static::$useGettext){
            return \gettext($text);
        }

        $text_domain = $text_domain ?? static::$currentTextDomain;
        $translation = static::$translations[$text_domain][$text] ?? $text;

        return !empty($translation) ? $translation : $text;
    }

    static function trans(string $text, $text_domain = null){
        return static::gettext($text, $text_domain);
    }

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

        if (!Strings::contains('_', $lang)){
            $only_lang = substr($lang, 0, 2);
            $lang_ix   = array_search($only_lang, $pure_langs);
            $selected  = $langs[$lang_ix];     
        } else {
            $lang_ix   = array_search($lang, $langs);

            if ($lang_ix !== false){
                $selected  = $langs[$lang_ix];    
            }
        }

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

        static::setLocale($selected, $encode);
    }

    // Nuevo: Pluralización
    static function transChoice(string $id, int $number, array $parameters = [], string $domain = null): string
    {
        $domain = $domain ?? static::$currentTextDomain;
        
        if (!isset(static::$pluralForms[$domain][$id])) {
            return $id;
        }

        $forms = static::$pluralForms[$domain][$id];
        $form = static::getPluralForm($number, count($forms));
        
        $translation = $forms[$form] ?? $id;
        return static::interpolate($translation, array_merge(['count' => $number], $parameters));
    }

    protected static function getPluralForm(int $n, int $totalForms): int
    {
        // Implementación básica - se puede expandir según el idioma
        return $n === 1 ? 0 : min(1, $totalForms - 1);
    }


    static function exportLangDef(bool $include_mo = true, string $locale_path = null, string $to = null, string $text_domain = null, string $preset = null)
    {   
        if ($locale_path === null){
            $locale_path = LOCALE_PATH;
        }

        $to = $to ?? $locale_path;
        $to = rtrim($to, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        foreach (new \DirectoryIterator($locale_path) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $lang = $fileInfo->getBasename();

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

                $intl = include $locale_path . "$lang/" . $def_file;

                switch ($preset){
                    case "wp":
                        $path_no_ext = "$to{$domain}-$lang";
                        $po_path     = "$path_no_ext.po";
                        $mo_path     = "$path_no_ext.mo";
                        break;
                    default:
                        Files::mkdir($to . "$lang/" . "LC_MESSAGES");
                        $po_path = $to . "$lang/" . "LC_MESSAGES/$domain.po";
                        $mo_path = $to . "$lang/" . "LC_MESSAGES/$domain.mo";
                }                

                $po_path = Files::normalize($po_path);
                $mo_path = Files::normalize($mo_path);

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
            
            foreach (new \DirectoryIterator($locale_path) as $fileInfo) {
                if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
                
                $dir = $fileInfo->getBasename();
                $path = $locale_path . DIRECTORY_SEPARATOR . $dir;
                
                foreach (new \DirectoryIterator($path) as $fileInfo) {
                    if($fileInfo->isDot()) continue;

                    $filename = "$path/$pot_domain.php";
                    $defs = include $pp;

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
                        $current_defs = include $filename;
                        $current_def_keys = array_keys($current_defs);
                        $not_incl_def_keys = array_diff($defs, $current_def_keys);

                        if (!empty($not_incl_def_keys)){
                            foreach ($not_incl_def_keys as $def){
                                $current_defs[$def] = "";
                            }
                            
                            StdOut::pprint("Actualizando $filename");
                            Files::varExport($current_defs, $filename);
                        }
                    }
                }
            }
        }
    }
}