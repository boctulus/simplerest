<?php
namespace simplerest\models;

use simplerest\core\Model;

class FolderModel extends Model 
{
	protected $table_name = "folder";
	protected $id_name = 'id';
	protected $fillable = ['resource_table', 'field', 'value', 'owner'];
  
	/*
		Types are INT, STR and BOOL among others
		see: https://secure.php.net/manual/en/pdo.constants.php 
	*/
	protected $schema = [
		'id' => 'INT',
		'resource_table' => 'STR',
		'field' => 'STR',
		'value' => 'STR',
		'owner' => 'INT'	
	];

    public function __construct($db = NULL){
		parent::__construct($db);
	}

}






