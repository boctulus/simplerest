<?php

namespace simplerest\models\az;


use simplerest\models\MyModel;
use simplerest\schemas\az\ApiKeysSchema;
use simplerest\core\traits\Uuids;

class ApiKeysModel extends MyModel
{
	use Uuids;
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ApiKeysSchema::class);
	}	
}

