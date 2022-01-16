<?php

use simplerest\core\libs\Strings;

function datetime(){
    $d = new \DateTime('', new \DateTimeZone(config()['DateTimeZone']));
    $at = $d->format('Y-m-d H:i:s'); // ok

    return $at;
}

// alias for datetime()
function at(bool $cached = true){
    static $date;
    
    if ($cached){
        if ($date === null){
            $date = datetime();
        }

        return $date;
    }
    
    return datetime();
}


