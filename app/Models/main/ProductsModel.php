<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\ProductsSchema;

class ProductsModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $table_name = 'products';

    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        if ($schema === null) {
            $schema = ProductsSchema::class;
        }
        parent::__construct($connect, $schema, $load_config);
	}
}

