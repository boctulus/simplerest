<?php

use simplerest\core\libs\DB;

function get_option($key){
    return table('options')
    ->where(['the_key' => $key])
    ->value('the_val');
}

function set_option($key, $val){
    if (get_option($key) === false){
        return table('options')
        ->noValidation()
        ->insert([
            'the_key' => $key,
            'the_val' => $val
        ]);
    } else {
        return table('options')
        ->noValidation()
        ->where(['the_key' => $key])
        ->update([
            'the_val' => $val
        ]);
    }

}