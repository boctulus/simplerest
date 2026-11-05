<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Interfaces;

interface Arrayable {

    /**
     * Get the instance as an array.
     *
     * @return array
     */
     function toArray();

}