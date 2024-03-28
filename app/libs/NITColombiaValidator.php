<?php

namespace simplerest\libs;

/*
    Si carece de digito verificador (longitud == 9) podria ser valido 
    pero no hay forma de saberlo

    Si $accept_with_no_verification_digit es true, se asume es valido
*/
class NITColombiaValidator
{
    public static function validate(int $nit, bool $accept_with_no_verification_digit = false) {
        $nit = trim((string) $nit);
        
        if (empty($nit)) {
            return false;
        }

        if (strlen($nit) == 9) {
            if ($accept_with_no_verification_digit){
                return true;
            }

            throw new \InvalidArgumentException("Falta dígito verificador");
        }

        if (strlen($nit) !== 10) {
            throw new \InvalidArgumentException("Longitud inválida");
        }

        $multipliers = [41, 37, 29, 23, 19, 17, 13, 7, 3];
        $v = 0;
        $sequence = str_split(substr($nit, 0, -1));

        foreach ($sequence as $index => $value) {
            $v += intval($value) * $multipliers[$index];
        }

        $v %= 11;

        if ($v >= 2) {
            $v = 11 - $v;
        }

        if (!isset($nit[9])){
            return false;
        }

        return strval($v) === $nit[9];
    }

}

