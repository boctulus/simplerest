<?php

namespace Boctulus\Simplerest\Core\Interfaces;

/*
    Similar a Countable pero count() es static
*/
interface ICountable {    
    static function count() : int;
}