<?php

use simplerest\core\libs\Config;


function config(){
    return Config::get();
}

function puff(){
    throw new \Exception("PUFF");
}


