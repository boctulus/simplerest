<?php

namespace simplerest\core\interfaces;

interface IProcessable extends ICountable
{
    static function run($query_sku = null, $offset = null, $limit = null);
    static function count() : int;
}

