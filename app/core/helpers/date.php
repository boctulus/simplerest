<?php

use simplerest\core\libs\Strings;

function datetime(string $format = 'Y-m-d H:i:s', $timezone = null){
    if ($timezone === null){    
        $timezone = new \DateTimeZone( date_default_timezone_get() );
    } else {
        if (is_string($timezone)){
            $timezone = new \DateTimeZone($timezone);
        }
    }

    $d  = new \DateTime('', $timezone);
    $at = $d->format($format); // ok

    return $at;
}

// alias for datetime()
function at(bool $cached = true){
    static $date;
    
    if ($cached){
        if ($date === null){
            $date = datetime('Y-m-d H:i:s');
        }

        return $date;
    }
    
    return datetime('Y-m-d H:i:s');
}


