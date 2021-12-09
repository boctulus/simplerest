<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblReteIcaSchema;

class TblReteIcaModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblReteIcaSchema::class);
	}	
}

