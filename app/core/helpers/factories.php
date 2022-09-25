<?php

use simplerest\core\libs\Factory;

function tag(string $name){
    return new simplerest\core\libs\HtmlBuilder\Tag($name);
}

function acl(){
    return Factory::acl();
}

function request(){
    return Factory::request();
}

function response($data = null, ?int $http_code = 200){
    return Factory::response($data, $http_code);
}

/*
    "Alias"
*/

function error($error = null, ?int $http_code = null, $detail = null){
    return response()->error($error, $http_code, $detail);
}
