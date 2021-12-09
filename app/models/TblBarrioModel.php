<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblBarrioSchema;

class TblBarrioModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblBarrioSchema::class);
	}	
}

