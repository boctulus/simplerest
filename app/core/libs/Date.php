<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Date
{
    /*
     salta sábados y domingos

     https://stackoverflow.com/a/5532070/980631
    */ 
    function nextWorkingDay(string $date, string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('+1 weekday');
        return $date->format($format);
    }

}