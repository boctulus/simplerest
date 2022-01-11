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

	function onDeleting(&$data)
	{
		d($data, 'deleting');
		// d($this->dd());
	}

	function onDeleted(array &$data, ?int $count)
	{
		d($count, 'deleted');
		// d($this->dd());
	}

	function onRestoring(array &$data)
	{
		//d($data, 'restoring');
		var_dump($data);
	}

	function onRestored(array &$data, ?int $count)
	{
		d($count, 'restored');
	}

	// function onReading()
	// {
	// 	$this->dontExec();
	// }

	// function onRead(int $count)
	// {
	// 	d($this->dd());
	// 	exit;
	// }
}

