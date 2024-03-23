<?php

namespace simplerest\core\interfaces;

/*
    Similar a Countable pero count() es static
*/
interface ICountable {    
    static function count() : int;
}