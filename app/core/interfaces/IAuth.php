<?php

namespace simplerest\core\interfaces;

interface IAuth {
    function login();
    function token();
    function register();
    function check();   
    
    function hasDbAcces($user_id, string $db_connection);
    function setCurrentUid($uid);
    function getCurrentUid();
    function setCurrentPermissions(Array $perms);
    function getCurrentPermissions();
    function setCurrentRoles(Array $roles);
    function getRoles();
}