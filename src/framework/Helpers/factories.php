<?php

use Boctulus\Simplerest\Core\Interfaces\IAcl;
use Boctulus\Simplerest\Core\Interfaces\IAuth;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Tag;
use Boctulus\Simplerest\Core\Request;

function tag(string $name) : Tag {
    return new Tag($name);
}

function acl() /* : IAcl */ {
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
