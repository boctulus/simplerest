<?php

use simplerest\core\interfaces\IAcl;
use simplerest\core\interfaces\IAuth;
use simplerest\core\libs\Factory;
use simplerest\core\libs\HtmlBuilder\Tag;
use simplerest\core\Request;

function tag(string $name) : Tag {
    return new Tag($name);
}

function acl() : IAcl {
    return Factory::acl();
}

function auth() : IAuth {
    return Factory::auth();
}

function request() : Request {
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
