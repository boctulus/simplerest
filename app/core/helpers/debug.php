<?php

use simplerest\core\libs\VarDump;

function show_debug_trace(){
    VarDump::showResponse();
}

function hide_debug_trace(){
    VarDump::hideResponse();
}

function d($val = null, $msg = null, bool $additional_carriage_return = false){
    if (VarDump::$render){
        $file = debug_backtrace()[1]['file'];
        $line = debug_backtrace()[1]['line'];
    
        VarDump::dd("{$file}:{$line}", "LOCATION", true);
    }

    return VarDump::dd($val, $msg, $additional_carriage_return);	
}	

function dd($val = null, $msg = null, bool $additional_carriage_return = true){
    return d($val, $msg, $additional_carriage_return);	
}

function here(){
    dd('HERE !');
}

