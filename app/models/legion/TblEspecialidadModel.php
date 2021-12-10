<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblEspecialidadSchema;

class TblEspecialidadModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblEspecialidadSchema::class);
	}	
}

