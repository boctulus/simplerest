<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\ProductsSchema;

class ProductsModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, ProductsSchema::class);
	}	
}

