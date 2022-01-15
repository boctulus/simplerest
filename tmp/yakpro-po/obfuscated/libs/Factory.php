<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-15 18:30:55              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; use simplerest\core\libs\Config; use simplerest\controllers\MyAuthController; class Factory { static function auth() { goto zp7ed; GqNx6: if (!($ScGji == null)) { goto HMOky; } goto Kgp2e; f06u0: return $ScGji; goto y7glr; xfWhf: HMOky: goto f06u0; zp7ed: static $ScGji; goto GqNx6; Kgp2e: $ScGji = new MyAuthController(); goto xfWhf; y7glr: } static function response() { return \simplerest\core\Response::getInstance(); } static function request() { return \simplerest\core\Request::getInstance(); } static function validador() { goto QETTy; wl9Yi: if (!($ScGji == null)) { goto HZFBt; } goto KNr7n; AWUtl: return $ScGji; goto pPAe3; jvl6a: HZFBt: goto AWUtl; KNr7n: $ScGji = new \simplerest\core\libs\Validator(); goto jvl6a; QETTy: static $ScGji; goto wl9Yi; pPAe3: } static function acl() { goto AqjMM; PbWT0: if (!($ScGji == null)) { goto hJaxD; } goto TAmYb; b3Rjl: return $ScGji; goto qZK9r; AqjMM: static $ScGji; goto PbWT0; TAmYb: $ScGji = (include CONFIG_PATH . "\x61\143\x6c\56\160\x68\160"); goto aDAvi; aDAvi: hJaxD: goto b3Rjl; qZK9r: } }
