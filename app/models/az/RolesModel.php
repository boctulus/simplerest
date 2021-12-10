<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\RolesSchema;

class RolesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, RolesSchema::class);
	}	
}

