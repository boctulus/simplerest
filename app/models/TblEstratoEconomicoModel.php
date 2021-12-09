<?php

namespace simplerest\models;

use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblEstratoEconomicoSchema;

class TblEstratoEconomicoModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblEstratoEconomicoSchema::class);
	}	
}

