<?php

use Boctulus\Simplerest\Core\Libs\Config;

function boom(string $msg){
    throw new \Exception($msg);
}

function puff(){
    throw new \Exception("PUFF");
}



