<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class Num
{
    static function normalize($values) {
        $totalWeight = array_sum($values);

        return array_map(function ($weight) use ($totalWeight) {
            return $weight / $totalWeight;
        }, $values);
     }


}

