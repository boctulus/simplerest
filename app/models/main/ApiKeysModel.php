<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\main\ApiKeysSchema;
use simplerest\traits\Uuids;

class ApiKeysModel extends MyModel
{ 
	use Uuids;	

	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, new ApiKeysSchema());
	}	
}

