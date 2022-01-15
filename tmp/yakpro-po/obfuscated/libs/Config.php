<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:55              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; class Config { protected static $data = []; protected static function setup() { static::$data = (include CONFIG_PATH . "\x63\157\x6e\146\x69\x67\56\160\150\160"); } static function get(?string $LqDJl = null) { goto JvaxB; cT66Q: A8FAc: goto fLuQQ; fLuQQ: if (!($LqDJl === null)) { goto tEjUG; } goto dsdlo; JvaxB: if (!empty(static::$data)) { goto A8FAc; } goto CrQj9; F49Ed: tEjUG: goto LcaYM; CrQj9: static::setup(); goto cT66Q; dsdlo: return static::$data; goto F49Ed; LcaYM: return static::$data[$LqDJl]; goto cTnzX; cTnzX: } static function set(string $LqDJl, $sKJd8) { goto MUCPJ; z2l7U: static::setup(); goto a28g4; QAw0W: static::$data[$LqDJl] = $sKJd8; goto lJqu6; MUCPJ: if (!empty(static::$data)) { goto TU1qG; } goto z2l7U; a28g4: TU1qG: goto QAw0W; lJqu6: } }
