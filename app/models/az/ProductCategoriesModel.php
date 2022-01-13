<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\az\ProductCategoriesSchema;

class ProductCategoriesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ProductCategoriesSchema::class);
	}	
}

