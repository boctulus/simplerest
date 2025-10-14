<?php

namespace Boctulus\Simplerest\Models\zippy;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\zippy\ProductsSchema;

class ProductsModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ProductsSchema::class);
	}	
}

