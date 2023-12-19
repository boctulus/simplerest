<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class RandomGenerator
{

    /*
        Objetivo:

        De acuerdo a los valores relativos en $values sera la probabilidad de obtener ese valor

        get([
            {valor} => {frecuencia},
            {valor} => {frecuencia},
            // ...
        ])

        Ej:

            $result = RandomGenerator::get(['A' => 20, 'B' => 80]);

        En este caso seria 20% vs 80% las probabilidades

        o

            get([
                2 => 234
                3 => 354
                4 => 500
                5 => 102
            ])

        En este caso las probabilidades de obtener 2, 3, 4 o 5 surgen de normalizar 234, 354, etc 
        o sea sus "pesos"
    */
    public static function get($values)
    {
        // Paso 1: Normalizar los pesos
        $normalized = Num::normalize($values);
        
        $n = mt_rand(0, 99);

        foreach ($normalized as $val => $w){
            $w = $w * 100;

            if ($w > $n){
                return $val;
            }
        }

        $keys = array_keys($values); 

        return $keys[rand(0, count($keys)-1)];
    }
}