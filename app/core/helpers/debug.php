<?php

use simplerest\core\libs\VarDump;

function show_debug_trace(bool $status = true){
    VarDump::showTrace($status);
}

function hide_debug_trace(){
    VarDump::hideTrace();
}

function show_debug_response(bool $status = true){
    VarDump::showResponse($status);
}

function hide_debug_response(){
    VarDump::hideResponse();
}

function d($val = null, $msg = null, bool $additional_carriage_return = false){
    return VarDump::dd($val, $msg, $additional_carriage_return);	
}	

function dd($val = null, $msg = null, bool $additional_carriage_return = true){
    return VarDump::dd($val, $msg, $additional_carriage_return);
}

function here(){
    dd('HERE !');
}

