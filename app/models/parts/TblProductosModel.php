<?php

namespace simplerest\models\parts;

use simplerest\models\MyModel;
use simplerest\schemas\parts\TblProductosSchema;

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

