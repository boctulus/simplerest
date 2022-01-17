<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Date
{
     
    function nextWorkingDay(string $date, string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('+1 weekday');
        return $date->format($format);
    }

}