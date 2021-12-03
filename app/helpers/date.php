<?php

use simplerest\libs\Strings;

function datetime(){
    $d = new \DateTime(NULL, new \DateTimeZone(config()['DateTimeZone']));
    $at = $d->format('Y-m-d H:i:s');

    return $at;
}