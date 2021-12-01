<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\az\Ts1Schema;

class Ts1Model extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new Ts1Schema());
	}	
}

