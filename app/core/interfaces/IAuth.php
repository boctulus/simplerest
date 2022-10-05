<?php

namespace simplerest\core\interfaces;

interface IAuth {
    function login();
    function token();
    function register();
    function check();   

    function setUID($uid);
    function uid();
    function setPermissions(Array $perms);
    function getPermissions();
    function setRoles(Array $roles);
    function getRoles();
}