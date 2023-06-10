<?php

use simplerest\core\libs\Config;

function boom(string $msg){
    throw new \Exception($msg);
}

function puff(){
    throw new \Exception("PUFF");
}



