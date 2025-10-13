<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\az\CablesSchema;

class CablesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, CablesSchema::class);
	}	
}

