<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Interfaces;

interface IMemoization {
    static function memoize($key, $callback_or_value = null, $expiration_time = null);
}