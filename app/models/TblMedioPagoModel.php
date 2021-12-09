<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblMedioPagoSchema;

class TblMedioPagoModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblMedioPagoSchema::class);
	}	
}

