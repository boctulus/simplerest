<?php

namespace Boctulus\Simplerest\Models\az;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\az\FacturaDetalleSchema;

class FacturaDetalleModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, FacturaDetalleSchema::class);
	}	
}

