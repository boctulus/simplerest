<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.13  |
    |              on 2022-01-16 20:17:43              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 declare (strict_types=1); namespace simplerest\core\libs; class Reflector { static array $info_constructor = []; static function getConstructor(string $FJi0J) { goto JNJS0; XTRmm: return static::$info_constructor[$FJi0J]; goto oM6eC; JUtgn: $cvFHv = new \ReflectionClass($FJi0J); goto Av2tE; mmGzD: $M6f_c = 0; goto cCLX8; JNJS0: if (!isset(static::$info_constructor[$FJi0J])) { goto srhzi; } goto XTRmm; cCLX8: $CFH9o = 0; goto qhU0s; KYiiY: return static::$info_constructor[$FJi0J]; goto UNmYq; mMRbU: PtXpX: goto Fk1DG; Av2tE: $HKBeY = $cvFHv->getConstructor()->getParameters(); goto Yi7Yd; Yi7Yd: $vTOlg = []; goto mmGzD; X6PBG: foreach ($HKBeY as $W2NLw => $w8Z9h) { goto F0Rv6; mu2nl: $cCV4T = $eAvxK === "\x63\x61\x6c\154\141\x62\x6c\145"; goto Hcl5R; sEGCH: $us3yw = $w8Z9h->isOptional(); goto afXN5; Hcl5R: $ENT3x = $w8Z9h->isPassedByReference(); goto Q1JNJ; afXN5: $vZ3DT = $w8Z9h->allowsNull(); goto VdawU; CNycq: $FJi0J = $w8Z9h->getName(); goto sEGCH; X9xEe: goto QQpv9; goto bpYLe; F0Rv6: $FQWVV = $w8Z9h->name; goto XeOdJ; Szkq_: WIgmW: goto x7znP; bwQtt: if (!$Vmspg) { goto WIgmW; } goto CVM5S; jiz_k: if (!$us3yw) { goto mItY6; } goto DObt7; XeOdJ: $eAvxK = $w8Z9h->getType(); goto CNycq; VFjzX: $DOmVo[] = ["\160\141\x72\x61\155\x5f\156\x61\x6d\x65" => $FQWVV, "\x72\145\x71\165\x69\162\145\x64" => !$us3yw, "\x69\163\137\x61\x72\162\x61\171" => $mkFg4]; goto CzjXx; CVM5S: $iG3Oc = $w8Z9h->getDefaultValue(); goto Szkq_; NjGqS: $M6f_c++; goto yFJHJ; DObt7: $CFH9o++; goto X9xEe; wE7nr: QQpv9: goto VFjzX; Q1JNJ: $vTOlg[] = $FQWVV; goto jiz_k; CzjXx: KeJUO: goto quBeq; bpYLe: mItY6: goto NjGqS; yFJHJ: $XD51U[] = $FQWVV; goto wE7nr; VdawU: $Vmspg = $w8Z9h->isDefaultValueAvailable(); goto bwQtt; x7znP: $mkFg4 = $eAvxK === "\141\x72\x72\x61\171"; goto mu2nl; quBeq: } goto mMRbU; v1G1_: $DOmVo = []; goto X6PBG; Fk1DG: static::$info_constructor[$FJi0J] = ["\160\141\162\141\x6d\x5f\x6e\141\x6d\x65\x73" => $vTOlg, "\x6f\160\164\x69\157\156\141\154\x5f\161\x74\171" => $CFH9o, "\x72\145\161\165\151\x72\145\144\x5f\x71\164\x79" => $M6f_c, "\x72\x65\x71\165\x69\162\145\x64\137\x70\x61\162\155\x73" => $XD51U, "\x70\141\x72\141\x6d\163" => $DOmVo]; goto KYiiY; qhU0s: $XD51U = []; goto v1G1_; oM6eC: srhzi: goto JUtgn; UNmYq: } }