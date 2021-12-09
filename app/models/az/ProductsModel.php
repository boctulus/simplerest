<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\az\ProductsSchema;

class ProductsModel extends MyModel
{ 	
	protected $createdAt = 'created_at';
	protected $createdBy = 'created_by';
	protected $updatedAt = 'updated_at';
	protected $updatedBy = 'updated_by';

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ProductsSchema::class);
	}	
}

