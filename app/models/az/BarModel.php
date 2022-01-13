<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\az\BarSchema;
use simplerest\core\traits\Uuids;

class BarModel extends MyModel
{ 
	use Uuids;	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, BarSchema::class);
	}	
}

