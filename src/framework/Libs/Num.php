<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class Num
{   
    /*
        Normaliza array de valores

        $normalized_values = Num::normalize([
            10, 6, 19
        ])
    */
    static function normalize(array $values) {
        $totalWeight = array_sum($values);

        return array_map(function ($weight) use ($totalWeight) {
            return $weight / $totalWeight;
        }, $values);
     }


}

