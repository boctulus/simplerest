<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class RandomGenerator
{

    /*
        Objetivo:

        De acuerdo a los valores relativos en $weights sera la probabilidad de obtener ese numero.

        Ej:

        getRandomIntegers(4, 5, [
            4 => 30
            5 => 70
        ])

        o

        getRandomIntegers(4, 5, [
            2 => 1
            3 => 0
            4 => 2
            5 => 3
        ])

        Terminar !
    */
    public static function getRandomIntegers($min, $max, $weights)
    {
        // Paso 1: Normalizar los pesos
        $totalWeight       = array_sum($weights);
        $normalizedWeights = array_map(function ($weight) use ($totalWeight) {
            return $weight / $totalWeight;
        }, $weights);

        
        # dd($normalizedWeights); // ok

        // < completar >
    }
}