<?php

namespace Boctulus\Simplerest\Models\az;


use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\az\ApiKeysSchema;
use Boctulus\Simplerest\Core\Traits\Uuids;

class ApiKeysModel extends MyModel
{
	use Uuids;
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ApiKeysSchema::class);
	}	
}

