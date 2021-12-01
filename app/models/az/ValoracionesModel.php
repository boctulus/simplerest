<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\az\ValoracionesSchema;

class ValoracionesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	function __construct(bool $connect = false){
        parent::__construct($connect, ValoracionesSchema::class);
	}	
}

