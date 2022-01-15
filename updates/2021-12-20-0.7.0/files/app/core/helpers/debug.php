<?php

use simplerest\core\libs\VarDump;

function dd($val, $msg = null, bool $additional_carriage_return = false){
    return VarDump::dd($val, $msg, $additional_carriage_return);	
}		

function d($val, $msg = null){
    return VarDump::dd($val, $msg, true);	
}

function here(){
    dd('HERE !');
}
