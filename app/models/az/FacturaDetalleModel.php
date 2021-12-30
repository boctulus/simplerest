<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\schemas\az\FacturaDetalleSchema;

class FacturaDetalleModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, FacturaDetalleSchema::class);
	}	
}

