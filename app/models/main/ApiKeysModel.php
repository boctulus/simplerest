<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\ApiKeysSchema;
use simplerest\traits\Uuids;

class ApiKeysModel extends MyModel
{
	use Uuids;
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ApiKeysSchema::class);
	}	
}

