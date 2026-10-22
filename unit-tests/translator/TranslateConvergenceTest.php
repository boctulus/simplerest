<?php

namespace Boctulus\Simplerest\Tests\Unit;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\i18n\Translate;
use PHPUnit\Framework\TestCase;

if (!defined('LOCALE_PATH')){
    define(
        'LOCALE_PATH',
        dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR
    );
}

class TranslateConvergenceTest extends TestCase
{
    private string $localeRoot;
    private array $previousTranslateState = [];
    private $previousConfigData;
    private $previousLocale;
    private $previousLanguage;
    private $previousLcAll;

    protected function setUp(): void
    {
        $this->previousLocale = setlocale(LC_ALL, '0');
        $this->previousLanguage = getenv('LANGUAGE');
        $this->previousLcAll = getenv('LC_ALL');

        foreach ([
            'currentTextDomain',
            'domainPaths',
            'translations',
            'currentLang',
            'useGettext',
            'ext_loaded',
            'fallbackLocale',
            'pluralForms',
            'domainUsesParser',
        ] as $propertyName){
            $property = new \ReflectionProperty(Translate::class, $propertyName);
            $this->previousTranslateState[$propertyName] = $property->getValue();
        }

        $configData = new \ReflectionProperty(Config::class, 'data');
        $this->previousConfigData = $configData->getValue();

        $this->localeRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'simplerest-translate-' . bin2hex(random_bytes(8));
        if (file_exists($this->localeRoot) || !mkdir($this->localeRoot, 0700, true)){
            throw new \RuntimeException('Unable to create isolated translation test directory');
        }

        $cachePath = $this->localeRoot . DIRECTORY_SEPARATOR . 'cache';
        if (!mkdir($cachePath, 0700)){
            throw new \RuntimeException('Unable to create isolated translation cache directory');
        }

        $configData->setValue(null, ['tmp_dir' => $cachePath]);
        $this->resetTranslateState();
    }

    protected function tearDown(): void
    {
        $this->restoreTranslateState();

        $configData = new \ReflectionProperty(Config::class, 'data');
        $configData->setValue(null, $this->previousConfigData);

        if ($this->previousLocale !== false){
            setlocale(LC_ALL, $this->previousLocale);
        }
        $this->restoreEnvironmentVariable('LANGUAGE', $this->previousLanguage);
        $this->restoreEnvironmentVariable('LC_ALL', $this->previousLcAll);

        $root = realpath(sys_get_temp_dir());
        $target = realpath($this->localeRoot);
        if (
            $root !== false &&
            $target !== false &&
            str_starts_with($target, $root . DIRECTORY_SEPARATOR) &&
            str_starts_with(basename($target), 'simplerest-translate-')
        ){
            $this->removeTree($target);
        }
    }

    public function testSetLocaleNormalizesCaseSeparatorAndEncoding(): void
    {
        Translate::useGettext(false);
        Translate::setLocale(' es-ar.UTF-8 ');

        $this->assertSame('es_AR', Translate::getLocale());
    }

    public function testResolveLocaleChainIncludesFallbackLanguageAndRegionalCatalog(): void
    {
        $this->createCatalog('en_US', 'messages', ['English only' => 'English']);
        $this->createCatalog('es', 'messages', ['Shared' => 'Español']);
        $this->createCatalog('es_AR', 'messages', ['Shared' => 'Che']);

        Translate::setFallbackLocale('en-us');

        $this->assertSame(
            ['en_US', 'es', 'es_AR'],
            Translate::resolveLocaleChain('ES-ar.UTF-8', $this->localeRoot)
        );
    }

    public function testLegacyRegionalCatalogDoesNotGainAnImplicitBaseFallback(): void
    {
        $this->createCatalog('en_US', 'messages', ['English only' => 'English']);
        $this->createCatalog('es_AR', 'messages', ['Local only' => 'Regional']);

        $this->assertSame(
            ['es_AR'],
            Translate::resolveLocaleChain('es_AR', $this->localeRoot)
        );
    }

    public function testBindMergesFallbackCatalogsAndRegionalOverrides(): void
    {
        $this->createCatalog('en_US', 'messages', [
            'English only' => 'English fallback',
            'Shared' => 'Default',
        ]);
        $this->createCatalog('es', 'messages', [
            'Shared' => 'Español',
            'Base only' => 'Solo base',
        ]);
        $this->createCatalog('es_AR', 'messages', [
            'Shared' => 'Che',
            'Base only' => '',
            'Local only' => 'Solo regional',
        ]);

        Translate::useGettext(false);
        Translate::setLocale('es-ar');
        Translate::bind('messages', $this->localeRoot);

        $this->assertSame('Che', Translate::trans('Shared'));
        $this->assertSame('Solo base', Translate::trans('Base only'));
        $this->assertSame('Solo regional', Translate::trans('Local only'));
        $this->assertSame('English fallback', Translate::trans('English only'));
    }

    public function testCompositeCatalogUsesParserWhenGettextIsEnabled(): void
    {
        if (!function_exists('gettext')){
            $this->markTestSkipped('The gettext extension is not available');
        }

        $this->createCatalog('en_US', 'messages', ['Fallback only' => 'English']);
        $this->createCatalog('es', 'messages', ['Shared' => 'Español']);
        $this->createCatalog('es_AR', 'messages', ['Shared' => 'Che']);

        Translate::useGettext(true);
        Translate::setLocale('es_AR');
        Translate::bind('messages', $this->localeRoot);

        $this->assertTrue(Translate::$useGettext);
        $this->assertTrue(Translate::$domainUsesParser['messages']);
        $this->assertSame('Che', Translate::trans('Shared'));
        $this->assertSame('English', Translate::trans('Fallback only'));
    }

    public function testSetLangSelectsNormalizedAvailableLocale(): void
    {
        Translate::useGettext(false);
        Translate::setLang('ES-ar.UTF-8');

        $this->assertSame('es_AR', Translate::getLocale());
    }

    private function createCatalog(string $locale, string $domain, array $translations): void
    {
        $directory = $this->localeRoot . DIRECTORY_SEPARATOR . $locale
            . DIRECTORY_SEPARATOR . 'LC_MESSAGES';
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)){
            throw new \RuntimeException('Unable to create translation catalog directory');
        }

        $contents = [];
        foreach ($translations as $message => $translation){
            $contents[] = 'msgid "' . addcslashes($message, "\\\"\n\r") . '"';
            $contents[] = 'msgstr "' . addcslashes($translation, "\\\"\n\r") . '"';
            $contents[] = '';
        }

        $catalogPath = $directory . DIRECTORY_SEPARATOR . $domain . '.po';
        if (file_put_contents($catalogPath, implode("\n", $contents)) === false){
            throw new \RuntimeException('Unable to write isolated translation catalog');
        }
    }

    private function resetTranslateState(): void
    {
        Translate::$currentTextDomain = null;
        Translate::$domainPaths = [];
        Translate::$translations = [];
        Translate::$currentLang = null;
        Translate::$useGettext = false;
        Translate::$ext_loaded = null;
        Translate::$fallbackLocale = 'en_US';
        Translate::$pluralForms = [];
        Translate::$domainUsesParser = [];
    }

    private function restoreTranslateState(): void
    {
        foreach ($this->previousTranslateState as $propertyName => $value){
            $property = new \ReflectionProperty(Translate::class, $propertyName);
            $property->setValue(null, $value);
        }
    }

    private function restoreEnvironmentVariable(string $name, $value): void
    {
        if ($value === false){
            putenv($name);
            return;
        }

        putenv($name . '=' . $value);
    }

    private function removeTree(string $path): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item){
            if ($item->isDir()){
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($path);
    }
}
