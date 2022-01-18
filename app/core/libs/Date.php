<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Date
{
    static function diffInSeconds(string $date2, string $date1 = ''){
        $d1 = new \DateTime($date1);
        $d2 = new \DateTime($date2);
    
        return $d2->getTimestamp() - $d1->getTimestamp();
    }
    
    static function nextYearFirstDay(string $date = '', string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('first day of next year');   
        return $date->format($format);
    }

    static function nextMonthFirstDay(string $date = '', string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('first day of next month');   
        return $date->format($format);
    }

    static function nextMonth(string $date = '', string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('+1 month');   
        return $date->format($format);
    }


    static function nextWeek(string $date = '', string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('+1 week');
        return $date->format($format);
    }

    /*
     salta sábados y domingos

     https://stackoverflow.com/a/5532070/980631
    */ 
    static function nextWorkingDay(string $date, string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('+1 weekday');
        return $date->format($format);
    }

    static function nextHour(string $date = '', string $format = 'Y-m-d'){
        $date = new \DateTime($date);
        $date->modify('+1 hour');
        return $date->format($format);
    }

}