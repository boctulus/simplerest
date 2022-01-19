<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Date
{
    static function getYear(string $date = '', bool $all_digits = true) : string {
        $do = new \DateTime($date);
        return $all_digits ? $do->format('Y') : $do->format('y');
    }

    static function getMonth(string $date = '', bool $leading_zeros = false) : string {
        $do = new \DateTime($date);
        return $leading_zeros ? $do->format('m') : $do->format('n');
    }

    static function getDay(string $date = '', bool $leading_zeros = false) : string {
        $do = new \DateTime($date);
        return $leading_zeros ? $do->format('d') : $do->format('j');
    }

    static function getWeekDay(string $date = '') : string {
        $do = new \DateTime($date);
        return $do->format('w');
    }

    static function diffInSeconds(string $date2, string $date1 = '') : int {
        $d1 = new \DateTime($date1);
        $d2 = new \DateTime($date2);
    
        return $d2->getTimestamp() - $d1->getTimestamp();
    }
    
    static function nextYearFirstDay(string $date = '', string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $d->modify('first day of next year');   
        return $d->format($format);
    }

    static function nextMonthFirstDay(string $date = '', string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $d->modify('first day of next month');   
        return $d->format($format);
    }

    static function nextMonth(string $date = '', string $format = 'Y-m-d') : string { 
        $d = new \DateTime($date);
        $d->modify('+1 month');   
        return $d->format($format);
    }


    static function nextWeek(string $date = '', string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $d->modify('+1 week');
        return $d->format($format);
    }

    // Next day, same hour
    static function nextDay(string $date = '', string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $d->modify('+1 day');
        return $d->format($format);
    }

    /*
     salta sábados y domingos

     https://stackoverflow.com/a/5532070/980631
    */ 
    static function nextWorkingDay(string $date, string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $d->modify('+1 weekday');
        return $d->format($format);
    }

    static function nextHour(string $date = '', string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $d->modify('+1 hour');
        return $d->format($format);
    }

    static function nextNthMonthFirstDay(int $month, string $date = '', string $format = 'Y-m-d') : string {
        $d = new \DateTime($date);
        $y = (int) $d->format('Y');
        $m = (int) $d->format('n');

        /*
            Si es el mismo mes, entrego el del siguiente año
        */
        if ($month <= $m){
            $y++;
            $strdate = "$y-$month-01";
        } else {
            if ($month > $m){
                $strdate = "$y-$month-01";
            }    
        }

        $d = new \DateTime($strdate);       
        return $d->format($format);
    }

    static function nextNthMonthDay(int $day, string $date = '', string $format = 'Y-m-d') : string {
        $_date = new \DateTime($date);
        $d = (int) $_date->format('j');
        $m = (int) $_date->format('n');
        $y = (int) $_date->format('Y');

        if ($day <= $d){
            $m++;
            if ($m>12){
                $m = 1;
            }

            $strdate = "$y-$m-$day";
        } else if ($day > $d){
            $strdate = "$y-$m-$day";
        } 


        $_date = new \DateTime($strdate);       
        return $_date->format($format);
    }


    static function nextNthWeekDay(int $weekday, string $date = '', string $format = 'Y-m-d') : string {
        $d1 = new \DateTime();
        $w1 = (int) $d1->format('w');

        if ($weekday == $w1){
            return $d1->modify('+1 week')->format($format);
        }

        if ($weekday < $w1){
            $diff = 7 - $w1 + $weekday;
            return $d1->modify("+$diff days")->format($format);
        }

        if ($weekday > $w1){
            $diff = $weekday - $w1;
            return $d1->modify("+$diff days")->format($format);
        }
    }
}