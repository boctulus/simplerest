<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Models\MyModel;
### IMPORTS

class ProductModel extends MyModel
{
	### TRAITS
	### PROPERTIES

	protected $hidden   = [];
	protected $not_fillable = [];
	protected $table_name = '__TABLE_NAME__';

    function __construct(bool $connect = false){
        parent::__construct($connect);
	}	
}

