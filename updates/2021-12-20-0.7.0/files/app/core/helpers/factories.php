<?php

use simplerest\core\libs\Factory;


function acl(){
    return Factory::acl();
}

function request(){
    return Factory::request();
}

function response(){
    return Factory::response();
}
