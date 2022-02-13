<?php

use simplerest\core\libs\VarDump;

function dd(mixed $val = null, $msg = null, bool $additional_carriage_return = false){
    return VarDump::dd($val, $msg, $additional_carriage_return);	
}		

function d(mixed $val = null, $msg = null){
    return VarDump::dd($val, $msg, true);	
}

function here(){
    dd('HERE !');
}
