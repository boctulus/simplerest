<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class RandomGenerator
{

    /*
        Objetivo:

        De acuerdo a los valores relativos en $values sera la probabilidad de obtener ese valor

        Ej:

        getRandomIntegers([
            4 => 30
            5 => 70
        ])

        En este caso seria 30% vs 70% las probabilidades

        o

        getRandomIntegers([
            2 => 1
            3 => 0
            4 => 2
            5 => 3
        ])

        En este caso las probabilidades surgen de normalizar
    */
    public static function getRandomIntegers($values)
    {
        // Paso 1: Normalizar los pesos
        $normalizednumbers = Num::normalize($values);

        $n = mt_rand(0, 99);

        foreach ($normalizednumbers as $val => $w){
            $w = $w * 100;

            if ($w > $n){
                return $val;
            }
        }

        $keys = array_keys($values); 

        return $keys[rand(0, count($keys)-1)];
    }
}