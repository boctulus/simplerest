<?php

declare(strict_types=1);
use Boctulus\Simplerest\Core\Libs\Config;

function boom(string $msg){
    throw new \Exception($msg);
}

function puff(){
    throw new \Exception("PUFF");
}



