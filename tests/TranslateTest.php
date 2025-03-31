<?php

namespace Boctulus\Simplerest\tests\Unit;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\i18n\Translate;
use Boctulus\Simplerest\Core\Libs\Files;

class TranslateTest extends TestCase
{
    private string $test_locale_path;

    protected function setUp(): void
    {
        define('LOCALE_PATH', '../app/locale' . DIRECTORY_SEPARATOR);
        
        // Reset static properties before each test
        $reflection = new \ReflectionClass(Translate::class);
        
        $properties = [
            'currentTextDomain',
            'domainPaths',
            'translations',
            'currentLang',
            'useGettext',
            'ext_loaded'
        ];

        foreach ($properties as $property) {
            $prop = $reflection->getProperty($property);
            $prop->setValue(null);
        }

        // Create temporary test locale directory
        $this->test_locale_path = LOCALE_PATH . 'test' . DIRECTORY_SEPARATOR;
        if (!is_dir($this->test_locale_path)) {
            mkdir($this->test_locale_path, 0777, true);
        }
    }

    public function test_check_gettext_loaded()
    {
        $result = Translate::checkGetTextLoaded(false);
        $this->assertIsBool($result);
    }

    public function test_use_gettext()
    {
        Translate::useGettext(true);
        $reflection = new \ReflectionClass(Translate::class);
        $property = $reflection->getProperty('useGettext');
        $this->assertEquals(
            function_exists('gettext'),
            $property->getValue()
        );
    }

    public function test_set_locale()
    {
        $lang = 'es_AR';
        Translate::setLocale($lang);
        $this->assertEquals($lang, Translate::getLocale());
    }

    public function test_bind_without_gettext()
    {
        Translate::useGettext(false);
        $domain = 'validator';
        
        $this->createTestPoFile('es_AR', $domain);
        
        Translate::setLocale('es_AR');
        $result = Translate::bind($domain);
        
        $this->assertTrue($result);
        $this->assertEquals($domain, Translate::getDomain());
    }

    public function test_translation_fallback_when_gettext_unavailable()
    {
        Translate::useGettext(false);
        $domain = 'validator';
        $originalText = 'Field is required';
        
        Translate::setLocale('es_AR');
        Translate::bind($domain);
        
        $result = Translate::trans($originalText);
        $this->assertNotEmpty($result);
    }

    public function test_set_lang_with_existing_locale()
    {
        Translate::setLang('es_AR');
        $this->assertEquals('es_AR', Translate::getLocale());
    }

    public function test_set_lang_fallback_to_language_only()
    {
        Translate::setLang('es');
        $locale = Translate::getLocale();
        $this->assertStringStartsWith('es', $locale);
    }

    public function test_multiple_domains()
    {
        Translate::useGettext(false);
        
        // Test with validator domain
        Translate::setLocale('es_AR');
        Translate::bind('validator');
        $this->assertEquals('validator', Translate::getDomain());
        
        // Test switching to import-quoter domain
        Translate::bind('import-quoter');
        $this->assertEquals('import-quoter', Translate::getDomain());
    }

    public function test_transient_error_logging()
    {
        // This test assumes gettext is not loaded
        $first_check = Translate::checkGetTextLoaded();
        $second_check = Translate::checkGetTextLoaded();
        
        // Both checks should return the same result
        $this->assertEquals($first_check, $second_check);
        
        // And transient should be set to prevent multiple logs
        $this->assertTrue(get_transient('gettext_extension_error_logged') !== false);
    }

    private function createTestPoFile(string $lang, string $domain)
    {
        $path = $this->test_locale_path . "$lang/LC_MESSAGES/";
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        file_put_contents($path . "$domain.po", 'msgid "Field is required"
msgstr "El campo es requerido"');
    }

    protected function tearDown(): void
    {
        // Cleanup test directory
        if (is_dir($this->test_locale_path)) {
            Files::rrmdir($this->test_locale_path);
        }
    }
}