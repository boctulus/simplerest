<?php

 declare (strict_types=1); namespace simplerest\core\libs; class Reflector { static array $info_constructor = []; static function getConstructor(string $Tn1GR) { goto CIkTX; g9Gqf: $HoSgf = []; goto fKTCz; C37o1: v5xXs: goto qgTj3; UAiji: return static::$info_constructor[$Tn1GR]; goto tdK58; fKTCz: $rWpYP = []; goto p0z_7; B3wl7: static::$info_constructor[$Tn1GR] = ["\160\x61\162\141\155\137\x6e\x61\155\x65\163" => $gFHBU, "\157\x70\x74\151\157\156\141\x6c\x5f\x71\x74\171" => $bAxDX, "\x72\x65\x71\165\151\162\x65\x64\x5f\161\x74\171" => $h9ki6, "\162\145\161\x75\x69\x72\145\144\x5f\160\141\162\x6d\163" => $HoSgf, "\x70\x61\x72\x61\x6d\x73" => $rWpYP]; goto UAiji; MfVIQ: $bAxDX = 0; goto g9Gqf; p0z_7: foreach ($KdSDF as $LIYNT => $VUofz) { goto uaDb0; A5E5S: $BnFe3 = $juXvc === "\143\x61\154\x6c\141\142\154\145"; goto kX69j; PTfLo: $juXvc = $VUofz->getType(); goto ATstp; CUEqR: if (!$Gzxha) { goto q5_VJ; } goto ijSVH; O1397: $HoSgf[] = $t3SnO; goto UZM5p; JeWwP: q2GZy: goto VTV73; KYtJq: $gFHBU[] = $t3SnO; goto CUEqR; feblQ: $noI7R = $VUofz->allowsNull(); goto sl7Cr; uaDb0: $t3SnO = $VUofz->name; goto PTfLo; aeJMq: q5_VJ: goto HHJUg; j1DDx: $Gzxha = $VUofz->isOptional(); goto feblQ; VTV73: $Xxa5t = $juXvc === "\141\162\162\x61\x79"; goto A5E5S; UZM5p: udJuv: goto JrivV; ATstp: $Tn1GR = $VUofz->getName(); goto j1DDx; ExmXW: goto udJuv; goto aeJMq; P4D1p: MGIxg: goto bnt_0; HHJUg: $h9ki6++; goto O1397; Ds492: if (!$jebN7) { goto q2GZy; } goto ncaUt; ncaUt: $hcT5Z = $VUofz->getDefaultValue(); goto JeWwP; ijSVH: $bAxDX++; goto ExmXW; sl7Cr: $jebN7 = $VUofz->isDefaultValueAvailable(); goto Ds492; JrivV: $rWpYP[] = ["\160\141\x72\x61\x6d\x5f\x6e\x61\155\145" => $t3SnO, "\162\x65\x71\x75\x69\x72\x65\x64" => !$Gzxha, "\x69\163\137\x61\162\162\141\x79" => $Xxa5t]; goto P4D1p; kX69j: $EVuqI = $VUofz->isPassedByReference(); goto KYtJq; bnt_0: } goto Tlbr4; qgTj3: $FHuyq = new \ReflectionClass($Tn1GR); goto l2Ml7; yufu_: $gFHBU = []; goto eWAjj; CIkTX: if (!isset(static::$info_constructor[$Tn1GR])) { goto v5xXs; } goto GYmtp; GYmtp: return static::$info_constructor[$Tn1GR]; goto C37o1; l2Ml7: $KdSDF = $FHuyq->getConstructor()->getParameters(); goto yufu_; Tlbr4: ccUfs: goto B3wl7; eWAjj: $h9ki6 = 0; goto MfVIQ; tdK58: } }