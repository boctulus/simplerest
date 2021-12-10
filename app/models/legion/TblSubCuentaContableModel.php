<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblSubCuentaContableSchema;

class TblSubCuentaContableModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblSubCuentaContableSchema::class);
	}	
}

