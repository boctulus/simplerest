<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\legion\TblCategoriaCuentaContableSchema;

class TblCategoriaCuentaContableModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblCategoriaCuentaContableSchema::class);
	}	
}

