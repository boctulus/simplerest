<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\Env;

/**
 * Parser de archivos .env (Env::parse / Env::parseValue / Env::parseFile).
 *
 * El parser reemplaza a parse_ini_file(), que fallaba con comentarios '#',
 * URLs con '&', valores con '=' y otros caracteres propios de archivos .env.
 *
 * Es una prueba pura: no requiere app.php ni base de datos.
 *
 * Ejecutar con: ./vendor/bin/phpunit unit-tests/env/EnvParserTest.php
 */
class EnvParserTest extends TestCase
{
    function test_parses_simple_key_value()
    {
        $this->assertSame(['APP_NAME' => 'SimplerRest'], Env::parse('APP_NAME=SimplerRest'));
    }

    function test_full_line_hash_comment_is_ignored()
    {
        $content = "# esto es un comentario\nAPP_ENV=local";

        $this->assertSame(['APP_ENV' => 'local'], Env::parse($content));
    }

    function test_full_line_semicolon_comment_is_ignored()
    {
        $content = "; comentario estilo ini\nAPP_ENV=local";

        $this->assertSame(['APP_ENV' => 'local'], Env::parse($content));
    }

    function test_comment_with_leading_whitespace_is_ignored()
    {
        $content = "   # comentario indentado\nAPP_ENV=local";

        $this->assertSame(['APP_ENV' => 'local'], Env::parse($content));
    }

    function test_inline_comment_on_unquoted_value_is_stripped()
    {
        $content = "DB_PORT=3306 # puerto de MySQL\nAPP_ENV=local ; otro estilo";

        $this->assertSame(
            ['DB_PORT' => '3306', 'APP_ENV' => 'local'],
            Env::parse($content)
        );
    }

    function test_hash_without_leading_whitespace_is_kept()
    {
        // Sin espacio antes del '#' no se considera comentario (comportamiento dotenv)
        $this->assertSame(['TOKEN' => 'abc#123'], Env::parse('TOKEN=abc#123'));
    }

    function test_semicolon_without_leading_whitespace_is_kept()
    {
        $this->assertSame(
            ['DSN' => 'mysql:host=127.0.0.1;dbname=test'],
            Env::parse('DSN=mysql:host=127.0.0.1;dbname=test')
        );
    }

    function test_hash_inside_quoted_value_is_kept()
    {
        $content = "URL=\"https://x.com/path#fragment\"\nPASS='#secreto'";

        $this->assertSame(
            ['URL' => 'https://x.com/path#fragment', 'PASS' => '#secreto'],
            Env::parse($content)
        );
    }

    function test_inline_comment_after_quoted_value_is_stripped()
    {
        $content = 'DB_PASSWORD="secreto" # no usar en prod';

        $this->assertSame(['DB_PASSWORD' => 'secreto'], Env::parse($content));
    }

    function test_double_quotes_are_stripped_and_spaces_preserved()
    {
        $this->assertSame(['GREETING' => 'hola mundo'], Env::parse('GREETING="hola mundo"'));
    }

    function test_single_quotes_are_literal()
    {
        // Las comillas simples no procesan escapes (semantica dotenv)
        $this->assertSame(['MSG' => 'a\nb'], Env::parse("MSG='a\\nb'"));
    }

    function test_double_quote_escapes()
    {
        $content = 'B="dice \"hola\""' . "\n" . 'C="ruta\\\\archivo"';

        $expected = [
            'B' => 'dice "hola"',
            'C' => 'ruta\\archivo',
        ];

        $this->assertSame($expected, Env::parse($content));
    }

    function test_backslash_n_inside_double_quotes_is_kept_literal()
    {
        // Las claves PEM se guardan con '\n' literal; no debe convertirse en salto de linea
        $this->assertSame(
            ['PEM' => '-----BEGIN-----\nabc\n-----END-----'],
            Env::parse('PEM="-----BEGIN-----\nabc\n-----END-----"')
        );
    }

    function test_unterminated_quote_keeps_raw_value()
    {
        $this->assertSame(['KEY' => '"sin cerrar'], Env::parse('KEY="sin cerrar'));
    }

    function test_empty_value_and_bare_key()
    {
        $content = "MAIL_PASSWORD=\nBARE_KEY";

        $this->assertSame(['MAIL_PASSWORD' => '', 'BARE_KEY' => ''], Env::parse($content));
    }

    function test_export_prefix_is_supported()
    {
        $this->assertSame(['FOO' => 'bar'], Env::parse('export FOO=bar'));
    }

    function test_section_headers_are_ignored()
    {
        $content = "[database]\nDB_HOST=127.0.0.1";

        $this->assertSame(['DB_HOST' => '127.0.0.1'], Env::parse($content));
    }

    function test_values_with_equals_and_special_chars_that_broke_parse_ini_file()
    {
        $content = "APP_URL=https://x.com/?a=1&b=2\nBASE64=abc==";

        $this->assertSame(
            ['APP_URL' => 'https://x.com/?a=1&b=2', 'BASE64' => 'abc=='],
            Env::parse($content)
        );
    }

    function test_blank_lines_and_crlf_are_handled()
    {
        $content = "A=1\r\n\r\nB=2\r\n";

        $this->assertSame(['A' => '1', 'B' => '2'], Env::parse($content));
    }

    function test_utf8_bom_is_stripped()
    {
        $this->assertSame(['A' => '1'], Env::parse("\xEF\xBB\xBFA=1"));
    }

    function test_keys_and_values_are_trimmed()
    {
        $this->assertSame(['KEY' => 'value'], Env::parse('  KEY  =  value  '));
    }

    function test_last_key_wins_on_duplicates()
    {
        $content = "KEY=old\nKEY=new";

        $this->assertSame(['KEY' => 'new'], Env::parse($content));
    }

    function test_the_real_env_example_file_parses()
    {
        $data = Env::parseFile(dirname(__DIR__, 2) . '/.env.example');

        $this->assertSame('SimpelRest', $data['APP_NAME']);
        $this->assertSame('local', $data['APP_ENV']);
        $this->assertSame('true', $data['APP_DEBUG']);
        $this->assertSame('4reasoning', $data['DB_PASSWORD']); // viene con comillas simples
        $this->assertSame('', $data['MAIL_DEFAULT_FROM_ADDR']);
        $this->assertArrayNotHasKey('#', $data);
    }

    function test_get_bool_reads_parsed_data()
    {
        $backup = Env::$data;

        try {
            Env::$data = Env::parse("APP_DEBUG=true\nMAIL_AUTH=off\nEMPTY=");

            $this->assertTrue(Env::getBool('APP_DEBUG'));
            $this->assertFalse(Env::getBool('MAIL_AUTH'));
            $this->assertFalse(Env::getBool('EMPTY'));
            $this->assertTrue(Env::getBool('MISSING', true));
        } finally {
            Env::$data = $backup;
        }
    }

    function test_parse_file_throws_on_missing_file()
    {
        $this->expectException(\Exception::class);

        Env::parseFile(dirname(__DIR__, 2) . '/no-existe.env');
    }
}
