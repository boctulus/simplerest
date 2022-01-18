<?php

use simplerest\core\libs\Strings;

function datetime(string $format = 'Y-m-d H:i:s'){
    $d = new \DateTime('', new \DateTimeZone(config()['DateTimeZone']));
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


