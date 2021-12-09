<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\schemas\az\Ts1Schema;

class Ts1Model extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, Ts1Schema::class);
	}	
}

