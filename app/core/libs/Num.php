<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class Num
{   
    /*
        $normalized_values = Num::normalize([
            10, 6, 19
        ])
    */
    static function normalize($values) {
        $totalWeight = array_sum($values);

        return array_map(function ($weight) use ($totalWeight) {
            return $weight / $totalWeight;
        }, $values);
     }


}

