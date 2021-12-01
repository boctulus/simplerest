<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\legion\TblFacturaDetalleSchema;

class TblFacturaDetalleModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new TblFacturaDetalleSchema());
	}
	
	function onCreating(array &$data)
	{
		//dd($this->createdBy(), 'CREATED BY');
		//dd($data, 'DATA TBL_FACTURA_DETALLE');
	}
}

