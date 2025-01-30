<?php

namespace Boctulus\ApiClient;

use Boctulus\ApiClient\Helpers\Strings;

class Utils
{
    static function firstNotEmpty($default_value = null, ...$args){
        foreach ($args as $val){
            if ($val !== null && $val !== ''){
                return $val;
            }
        }

        return $default_value;
    }

    /*
        Ej:

        $const_name = Utils::getConstants(10018, 'curl');  // CURLOPT_USERAGENT
    */
    static function getConstantName(int $value, string $domain){
        $constants = get_defined_constants(true)[$domain];
        $name      = array_search($value, $constants, TRUE);  

        return $name;
    }
}

