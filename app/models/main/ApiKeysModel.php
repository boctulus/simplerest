<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\main\ApiKeysSchema;
use simplerest\core\traits\Uuids;

class ApiKeysModel extends MyModel
{ 
	use Uuids;	

	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, ApiKeysSchema::class);
	}	
}

