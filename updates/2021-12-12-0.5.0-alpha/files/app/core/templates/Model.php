<?php

namespace simplerest\models;


use simplerest\models\MyModel;
### IMPORTS

class __NAME__ extends MyModel
{
	### TRAITS
	### PROPERTIES

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, __SCHEMA_CLASS__::class);
	}	
}

