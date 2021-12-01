<?php

namespace simplerest\models;

use simplerest\libs\ValidationRules;
use simplerest\models\schemas\az\ProductTagsSchema;

// Modelo de mi Detalle
class ProductTagsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new ProductTagsSchema());
	}
	
	function onCreated(array &$data, $last_inserted_id)
	{
		dd($GLOBALS['name_module'], 'NAME MODULE');;
	}
}

