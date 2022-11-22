<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\az\ProductsSchema;

class ProductsModel extends MyModel
{ 	
	protected $createdAt = 'created_at';
	protected $createdBy = 'created_by';
	protected $updatedAt = 'updated_at';
	protected $updatedBy = 'updated_by';

	protected $hidden   = [
		"is_locked" 
	];
	
	protected $not_fillable = [];

	protected $field_names = [
		"is_locked"   => "Locked?",
        "created_at"  => "Creation Date",
        "updated_at"  => "Update Date", 
		"how_popular" => "Popularity"
    ];

	/*
		Aca se especificaria si es un checkbox o radiobox por ejemplo

		Para tipo "dropdown" o "list" se utilizarian los valores de la regla de validacion
		o de la tabla relacionada 

		Tambien otros formatters que puedan estar disponibles en el frontend
	*/
	protected $formatters = [
		"is_locked"     => "checkbox",
		"active"        => "radio",
		"rating"        => "starts",
		"how_popular"   => "progress"
	];

    function __construct(bool $connect = false){
        parent::__construct($connect, ProductsSchema::class);
	}	

	function costScope(){
		$this->where(['cost', 100, '>=']);
		return $this;
	}

	function onDeleting(&$data)
	{
		//d($data, 'deleting');
		// d($this->dd());
	}

	function onDeleted(array &$data, ?int $count)
	{
		//d($count, 'deleted');
		// d($this->dd());
	}

	function onRestoring(array &$data)
	{
		//d($data, 'restoring');
		//var_dump($data);
	}

	function onRestored(array &$data, ?int $count)
	{
		//d($count, 'restored');
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

