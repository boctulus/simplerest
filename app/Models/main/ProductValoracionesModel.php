<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\main\ProductValoracionesSchema;

class ProductValoracionesModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';
	protected $updatedAt = 'updated_at';

	function __construct(bool $connect = false){
        parent::__construct($connect, ProductValoracionesSchema::class);
	}	
}

