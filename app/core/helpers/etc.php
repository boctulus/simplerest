<?php

use simplerest\core\libs\Config;


function config(){
    return Config::get();
}

function boom(string $msg){
    throw new \Exception($msg);
}

function puff(){
    throw new \Exception("PUFF");
}


