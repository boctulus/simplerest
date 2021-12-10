<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\legion\TblFacturaDetalleSchema;

class TblFacturaDetalleModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblFacturaDetalleSchema::class);
	}
	
	function onCreating(array &$data)
	{
		//dd($this->createdBy(), 'CREATED BY');
		//dd($data, 'DATA TBL_FACTURA_DETALLE');
	}
}

