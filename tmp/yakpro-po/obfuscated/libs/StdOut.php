<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:56              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; class StdOut { static $_printable = true; static function pprint($s11DD, bool $A02ds = false) { goto CSMb0; CSMb0: if (!self::$_printable) { goto jNC3U; } goto AP4j9; AP4j9: dd($s11DD, null, $A02ds); goto Usurb; Usurb: jNC3U: goto fdChO; fdChO: } static function setPrintable(bool $yZuRe) { self::$_printable = $yZuRe; } static function hideResponse() { static::setPrintable(false); } static function showResponse() { static::setPrintable(true); } }
