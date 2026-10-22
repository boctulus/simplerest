<?php

namespace Boctulus\Simplerest\Tests\Translator;

use Boctulus\Simplerest\Core\Libs\LangDetector;
use PHPUnit\Framework\TestCase;

class LangDetectorTest extends TestCase
{
    public function test_supported_languages_include_generic_additions(): void
    {
        $this->assertSame(['en', 'es', 'it', 'hi', 'bn'], LangDetector::langs());
    }

    public function test_empty_input_has_no_score_or_detected_language(): void
    {
        $this->assertSame(0, LangDetector::is('', 'en'));
        $this->assertSame(0, LangDetector::is('---', 'es'));
        $this->assertNull(LangDetector::detect(''));
    }

    public function test_detect_identifies_latin_languages(): void
    {
        $this->assertSame(
            'en',
            LangDetector::detect('This is a simple message for all users.')
        );
        $this->assertSame(
            'es',
            LangDetector::detect('Este es un mensaje sencillo para todos.')
        );
        $this->assertSame(
            'it',
            LangDetector::detect('Questo è un messaggio semplice per tutti.')
        );
    }

    public function test_detect_identifies_devanagari_and_bengali_scripts(): void
    {
        $this->assertSame('hi', LangDetector::detect('यह एक साधारण परीक्षण है'));
        $this->assertSame('bn', LangDetector::detect('এটি একটি সাধারণ পরীক্ষা'));
    }
}
