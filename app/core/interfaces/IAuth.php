<?php

namespace simplerest\core\interfaces;

interface IAuth {
    function login();
    function token();
    function register();
    function check();   

    function uid();
    function getPermissions();
    function getRoles();
    function isGuest() : bool;
    function isRegistered() : bool;
}