<?php

namespace Boctulus\Simplerest\Core\Libs\i18n;

/*
    @author Pablo Bozzolo <boctulus@gmail.com>
*/

use Boctulus\Simplerest\Core\Container;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\i18n\POParser;
use Boctulus\Simplerest\Core\Libs\System;

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
    static $domainUsesParser  = [];

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
        static::$currentLang = static::normalizeLocale($lang);

        if (static::$useGettext){
            setlocale(LC_ALL, static::$currentLang . ".$encoding");
            putenv('LANGUAGE=' . static::$currentLang . ".$encoding");
            putenv('LC_ALL=' . static::$currentLang);
        }
    }

    static function getLocale(){
        return static::$currentLang;
    }

    static function getDomain(){
        return static::$currentTextDomain;
    }

    static function setFallbackLocale(string $locale){
        static::$fallbackLocale = static::normalizeLocale($locale);
    }

    static function getLocaleChain(){
        if (static::$currentLang === null){
            return [];
        }

        return static::resolveLocaleChain(static::$currentLang);
    }

    static function resolveLocaleChain(string $locale, string $path = LOCALE_PATH): array
    {
        $locale   = static::normalizeLocale($locale);
        $language = static::getLanguage($locale);
        $availableLocales = static::getAvailableLocales($path);

        // Keep legacy regional catalogs complete until a language-level catalog exists.
        if (!isset($availableLocales[$language])){
            return [$locale];
        }

        $chain = [];
        $fallback = static::normalizeLocale(static::$fallbackLocale);

        if ($fallback !== $language && $fallback !== $locale){
            $chain[] = $fallback;
        }

        $chain[] = $language;

        if ($locale !== $language){
            $chain[] = $locale;
        }

        return array_values(array_unique($chain));
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

        if (static::$useGettext && !static::usesCompositeCatalog($path)){
            static::$domainUsesParser[$domain] = false;
            bindtextdomain($domain, $path);
        } else {
            static::$domainUsesParser[$domain] = true;
            static::$translations[$domain] = static::loadDomainTranslations($domain, $path);
        }

        return static::setTextDomain($domain);
    }

    static function setTextDomain(string $domain) : bool {
        if (!isset(static::$domainPaths[$domain])){
            throw new \InvalidArgumentException("Domain '$domain' was not binded");
        }

        if (static::$useGettext && !(static::$domainUsesParser[$domain] ?? false)){
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

        $text_domain = $text_domain ?? static::$currentTextDomain;

        if (static::$useGettext && !(static::$domainUsesParser[$text_domain] ?? false)){
            return \gettext($text);
        }

        $translation = static::$translations[$text_domain][$text] ?? $text;

        return !empty($translation) ? $translation : $text;
    }

    static function trans(string $text, $text_domain = null){
        return static::gettext($text, $text_domain);
    }

    static function setLang(?string $lang){
        if ($lang === null || $lang === '*'){
            return;
        }

        try {
            $requested = static::normalizeLocale($lang);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $availableLocales = static::getAvailableLocales(LOCALE_PATH);
        $selected = $availableLocales[$requested] ?? null;
        $language = static::getLanguage($requested);

        if ($selected === null && isset($availableLocales[$language])){
            $selected = $availableLocales[$language];
        }

        if ($selected === null){
            foreach ($availableLocales as $normalized => $available){
                if (static::getLanguage($normalized) === $language){
                    $selected = $available;
                    break;
                }
            }
        }

        if ($selected !== null){
            static::setLocale($selected);
        }
    }

    protected static function normalizeLocale(string $locale): string
    {
        $locale = trim($locale);
        $locale = explode('.', $locale, 2)[0];
        $locale = str_replace('-', '_', $locale);
        $parts = explode('_', $locale, 2);
        $language = strtolower($parts[0]);

        if ($language === '' || !preg_match('/^[a-z]{2,3}$/', $language)){
            throw new \InvalidArgumentException("Invalid locale '$locale'");
        }

        if (!isset($parts[1]) || $parts[1] === ''){
            return $language;
        }

        if (!preg_match('/^[a-z0-9]{2,8}$/i', $parts[1])){
            throw new \InvalidArgumentException("Invalid locale '$locale'");
        }

        return $language . '_' . strtoupper($parts[1]);
    }

    protected static function getLanguage(string $locale): string
    {
        return explode('_', $locale, 2)[0];
    }

    protected static function getAvailableLocales(string $path): array
    {
        $path = rtrim($path, '/\\');

        if (!is_dir($path)){
            return [];
        }

        $locales = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isDir()){
                continue;
            }

            try {
                $normalized = static::normalizeLocale($fileInfo->getBasename());
            } catch (\InvalidArgumentException $e) {
                continue;
            }

            $locales[$normalized] = $fileInfo->getBasename();
        }

        ksort($locales);
        return $locales;
    }

    protected static function usesCompositeCatalog(string $path): bool
    {
        if (static::$currentLang === null){
            return false;
        }

        $language = static::getLanguage(static::$currentLang);
        $availableLocales = static::getAvailableLocales($path);

        return isset($availableLocales[$language]);
    }

    protected static function loadDomainTranslations(string $domain, string $path): array
    {
        if (static::$currentLang === null){
            return [];
        }

        Container::singleton('po_parser', POParser::class);
        $parser = Container::make('po_parser');
        $translations = [];
        $availableLocales = static::getAvailableLocales($path);
        $path = rtrim($path, '/\\');

        foreach (static::resolveLocaleChain(static::$currentLang, $path) as $locale){
            $directory = $availableLocales[$locale] ?? $locale;
            $file = $path . DIRECTORY_SEPARATOR . $directory
                . DIRECTORY_SEPARATOR . 'LC_MESSAGES'
                . DIRECTORY_SEPARATOR . $domain . '.po';

            if (!is_file($file)){
                continue;
            }

            $catalog = $parser->parse($file);
            if (!is_array($catalog)){
                Logger::logError("Unable to parse translation catalog '$file'");
                continue;
            }

            $catalog = array_filter($catalog, function ($translation){
                if (is_array($translation)){
                    return count(array_filter($translation, function ($form){
                        return $form !== null && $form !== '';
                    })) > 0;
                }

                return $translation !== null && $translation !== '';
            });

            $translations = array_replace($translations, $catalog);
        }

        return $translations;
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

                StdOut::print("Generating $po_path");

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
                    StdOut::print("Compiling to $mo_path");
                    exec("msgfmt $po_path -o $mo_path", $output, $exit_code);
                    StdOut::print("Compilation to $mo_path " . ($exit_code === 0 ? '-- ok' : '-- error'));
                    StdOut::print('');
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
                StdOut::print("File '$text_domain.pot.php' not found");
                exit;
            }
        } else {
            $php_pot = glob($locale_path . '*.pot.php');
        }

        if (empty($php_pot)){
            StdOut::print("No *.pot.php files found");
            exit;
        }
        
        foreach ($php_pot as $pp){
            $pot_domain = Strings::beforeIfContains(basename($pp), '.pot.php');
            $defs = include $pp;
            $availableLocales = static::getAvailableLocales($locale_path);

            foreach (new \DirectoryIterator($locale_path) as $fileInfo) {
                if($fileInfo->isDot() || !$fileInfo->isDir()) continue;

                $dir = $fileInfo->getBasename();
                $path = $locale_path . DIRECTORY_SEPARATOR . $dir;

                try {
                    $normalized = static::normalizeLocale($dir);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }

                $language = static::getLanguage($normalized);
                $isRegionalOverride = $normalized !== $language
                    && isset($availableLocales[$language]);
                $filename = "$path/$pot_domain.php";

                // Regional catalogs are sparse only when their language base exists.
                if ($isRegionalOverride){
                    continue;
                }

                if (!file_exists($filename)){
                    StdOut::print("Creando archivo $pot_domain.php en $dir");

                    $p_defs = [];
                    foreach ($defs as $def){
                        $clean_def = str_replace([
                            ':/f/',
                            ':/m/'
                        ], '', trim($def));

                        $p_defs[$def] = ($normalized === 'en_US') ? $clean_def : "";
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

                        StdOut::print("Actualizando $filename");
                        Files::varExport($current_defs, $filename);
                    }
                }
            }
        }
    }
}
