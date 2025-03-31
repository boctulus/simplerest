<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\interfaces\IVehicle;

class Car implements IVehicle
{
    protected int $max_speed;
    protected string $color;

    function __construct(string $color = 'grey', int $max_speed) {
        $this->color = $color;
        $this->max_speed = $max_speed;
    }

    function run(){
        dd("Running to reach {$this->max_speed} km/h");
    }

    function stop(){
        dd("Going to zero");
    }
}