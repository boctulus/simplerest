<?php

use simplerest\core\libs\Url;
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

/*
    Ej:

    print_array($header, 'CABECERAS');
    print_array($header, 'CABECERAS', '. ');  

    En este segundo caso imprime algo como:

    --| CABECERAS
    . ID
    . Tipo
    . SKU
    . Nombre
    . Publicado

    Otro ejemplo:

    print_array(array_column($rows,'Estado del impuesto'), '', '. ');
*/
function print_array($array, $msg = null, $prepend = '', bool $additional_carriage_return = true, bool $msg_at_top = true){
    $cli     = (php_sapi_name() == 'cli');
    $br      = VarDump::br();
    $p       = VarDump::p();

    $pre = !$cli;	

    if (!empty($msg)){
        $cfg = config();
        $ini = $cfg['var_dump_separators']['start'] ?? '--| ';
        $end = $cfg['var_dump_separators']['end']   ?? '';
    }

    if (!empty($msg) && $msg_at_top){
        echo "{$ini}$msg{$end}". (!$pre ? $br : '');
        echo !empty($prepend) ? $br : '';
    }

    foreach ($array as $k => $v){
        echo $prepend;
        dd($v, null, false);
    }

    if (!empty($msg) && !$msg_at_top){
        echo "{$ini}$msg{$end}". (!$pre ? $br : '');
    }

    if ($additional_carriage_return){
        echo $br;
    }
}

