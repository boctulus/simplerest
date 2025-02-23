<?php

namespace simplerest\models\edu;

use simplerest\models\MyModel;
use simplerest\schemas\edu\CategoriesSchema;

class CategoriesModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, CategoriesSchema::class);
	}	
}

