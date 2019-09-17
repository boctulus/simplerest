<?php

function shift(&$arr, $key, $default_value = NULL)
{
    $out = $arr[$key] ?? $default_value;
    unset($arr[$key]);
    return $out;
}