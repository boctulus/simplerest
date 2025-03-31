<?php

namespace Boctulus\Simplerest\Models\legion;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\legion\TblFacturaDetalleSchema;

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

