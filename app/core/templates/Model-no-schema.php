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
	protected $table_name = '__TABLE_NAME__';

    function __construct(bool $connect = false){
        parent::__construct($connect);
	}	
}

