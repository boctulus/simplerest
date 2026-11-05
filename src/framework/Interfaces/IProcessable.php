<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Interfaces;

interface IProcessable extends ICountable
{
    static function run($filter = null, $offset = null, $limit = null);
    static function count() : int;
}

