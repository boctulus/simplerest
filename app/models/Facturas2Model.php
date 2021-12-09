<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\schemas\az\Facturas2Schema;

class Facturas2Model extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, Facturas2Schema::class);
	}	
}

