<?php

namespace simplerest\core\interfaces;

interface IProcessable extends ICountable
{
    static function run($filter = null, $offset = null, $limit = null);
    static function count() : int;
}

