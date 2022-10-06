<?php

namespace simplerest\core\interfaces;

interface IAuth {
    function login();
    function token();
    function register();
    function check();   

    function uid();
    function setPermissions(Array $perms);
    function getPermissions();
    function setRoles(Array $roles);
    function getRoles();
    function isGuest() : bool;
    function isRegistered() : bool;
}