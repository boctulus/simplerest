<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
### IMPORTS

class __NAME__ extends MyModel
{ 
	### TRAITS
	### PROPERTIES

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new __SCHEMA_CLASS__());
	}	
}

