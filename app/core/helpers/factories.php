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

function response(){
    return Factory::response();
}
