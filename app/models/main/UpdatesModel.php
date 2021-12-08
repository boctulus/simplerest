<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\schemas\main\UpdatesSchema;
use simplerest\traits\Uuids;

class UpdatesModel extends MyModel
{
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $createdAt = 'created_at';

	use Uuids;
		
	function __construct(bool $connect = false){
        parent::__construct($connect, UpdatesSchema::class);
	}	
}

