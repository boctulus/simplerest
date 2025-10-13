<?php

namespace Boctulus\Simplerest\Models\parts;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\parts\TblProductosSchema;

class TblProductosModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblProductosSchema::class);
	}	
}

