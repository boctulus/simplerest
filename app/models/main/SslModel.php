<?php

namespace simplerest\models\main;


use simplerest\models\MyModel;
use simplerest\schemas\main\SslSchema;

class SslModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];

	protected $createdAt = 'created_at';
	protected $updatedAt = 'updated_at';
	protected $deletedAt = 'deleted_at'; 

    function __construct(bool $connect = false){
        parent::__construct($connect, SslSchema::class);
	}	
}

