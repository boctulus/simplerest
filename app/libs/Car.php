<?php

namespace simplerest\libs;

use simplerest\interfaces\Vehicle;


class Car implements Vehicle
{
    protected int $max_speed;
    protected string $color;

    function __construct(string $color = 'grey', int $max_speed) {
        $this->color = $color;
        $this->max_speed = $max_speed;
    }

    function run(){
        d("Running to reach {$this->max_speed} km/h");
    }

    function stop(){
        d("Going to zero");
    }
}

