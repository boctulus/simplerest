<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;

/*
	Example class
    
    @author Isaac Souza
*/
class Cake
{
    private $ingredient1;
    private $ingredient2;
    private $cook_time; // minutes

    public function __construct(Ingredient1 $ingredient1, Ingredient2 $ingredient2, $cook_time)
    {
        $this->cook_time   = $cook_time;
        $this->ingredient1 = $ingredient1;
        $this->ingredient2 = $ingredient2;
    }

    public function get()
    {
        return 'Cake 2 is ready.';
    }
}