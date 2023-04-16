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

function dd($val = null, $msg = null, bool $additional_carriage_return = true, bool $msg_at_top = true){
    return VarDump::dd($val, $msg, $additional_carriage_return, $msg_at_top);
}

function here(){
    dd('HERE !');
}

