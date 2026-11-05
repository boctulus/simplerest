<?php

declare(strict_types=1);
function pre(?string $str = null, ?string $id = null){
    if ($id !== null){
        return "<pre id=\"$id\">$str</str>";
    }

    return '<pre>' . $str . '</str>';
}

function p(?string $str = null){
    return '<p>'.$str.'</p>';
}

function br(){
    return '<br/>';
}


