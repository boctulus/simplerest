<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\az\BarSchema;
use simplerest\traits\Uuids;

class BarModel extends MyModel
{ 
	use Uuids;	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new BarSchema());
	}	
}

