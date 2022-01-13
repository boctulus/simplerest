<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\legion\TblCotizaciondetalleSchema;

class TblCotizaciondetalleModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblCotizaciondetalleSchema::class);
	}	
}

