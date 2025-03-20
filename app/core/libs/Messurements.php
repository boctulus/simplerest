<?php

namespace simplerest\core\libs;

/*
    Unit conversion library

    @author Pablo Bozzolo <boctulus@gmail.com>
*/

class Messurements
{
    static function feetToInches(float $feets, $inches = 0) {
        $feet_into_inches = (float) $feets * 12;
        $inches           = (float) $inches;
       
        return $feet_into_inches + $inches;
    }

    static function inchesToFeet(float $inches){
        return $inches / 12;
    }

    static function inchesToFeetAndInches(float $inches) {
        $div = $inches / 12;
        $feets  = floor($div);
        $inches = floor(($div - $feets) * 12);

        return ($inches == 0) ? "$feets'" : "$feets'$inches''";
    }
}

