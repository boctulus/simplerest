<?php

use simplerest\libs\Strings;

function datetime(){
    $d = new \DateTime('', new \DateTimeZone(config()['DateTimeZone']));
    $at = $d->format('Y-m-d H:i:s'); // ok

    return $at;
}

// alias for datetime()
function at(){   
    return datetime();
}


