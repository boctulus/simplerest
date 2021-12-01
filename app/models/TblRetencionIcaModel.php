<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\legion\TblRetencionIcaSchema;

class TblRetencionIcaModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblRetencionIcaSchema());
	}	
}

