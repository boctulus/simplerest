<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\ApiKeysSchema;
use simplerest\traits\Uuids;

class ApiKeysModel extends MyModel
{ 
	use Uuids;
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new ApiKeysSchema());
	}	
}

