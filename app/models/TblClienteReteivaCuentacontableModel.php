<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\legion\TblClienteReteivaCuentacontableSchema;

class TblClienteReteivaCuentacontableModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblClienteReteivaCuentacontableSchema());
	}	
}
