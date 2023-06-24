<?php

namespace simplerest\core\libs;

/*
    Mejor que nada..... 

    Ventaja: no utiliza extensiones de PHP que pueden no estar instaladas
*/

class SimpleCrypt {
    public static function encrypt($str) {
        $length = strlen($str);
        $encrypted = '';
        
        // Realizar translocaciones reversibles de subcadenas
        for ($i = 0; $i < $length; $i += 2) {
            $substring = substr($str, $i, 2);
            $encrypted .= strrev($substring); // Revertir subcadena
        }
        
        return $encrypted;
    }

    public static function decrypt($str) {
        $length = strlen($str);
        $decrypted = '';
        
        // Revertir las translocaciones reversibles de subcadenas
        for ($i = 0; $i < $length; $i += 2) {
            $substring = substr($str, $i, 2);
            $decrypted .= strrev($substring); // Revertir subcadena
        }
        
        return $decrypted;
    }
}



