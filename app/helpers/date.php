<?php

use simplerest\libs\Strings;

function datetime(){
    $d = new \DateTime(NULL, new \DateTimeZone(config()['DateTimeZone']));
    $at = $d->format('Y-m-d G:i:s');

    return $at;
}