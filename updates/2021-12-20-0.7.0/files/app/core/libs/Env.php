<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:56              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; class Env { static $data; static function setup() { goto laIPR; laIPR: if (empty($_ENV)) { goto SqNOG; } goto NgTxa; lOzea: SqNOG: goto RNoD8; NgTxa: static::$data = $_ENV; goto lOzea; RNoD8: static::$data = parse_ini_file(ROOT_PATH . "\56\145\156\x76"); goto dTqbW; dTqbW: } static function get(?string $JT4F5 = null) { goto tc6uU; xkU14: static::setup(); goto iRBfQ; iRBfQ: Y0gjj: goto Zwlry; tc6uU: if (!empty(static::$data)) { goto Y0gjj; } goto xkU14; E_v0W: return static::$data; goto uXBHq; uXBHq: I9Nsg: goto At7im; At7im: return static::$data[$JT4F5]; goto DRcNG; Zwlry: if (!empty($JT4F5)) { goto I9Nsg; } goto E_v0W; DRcNG: } }
