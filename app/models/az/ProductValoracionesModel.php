<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\az\ProductValoracionesSchema;

class ProductValoracionesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';
	protected $updatedAt = 'updated_at';

	function __construct(bool $connect = false){
        parent::__construct($connect, new ProductValoracionesSchema());
	}	
}
