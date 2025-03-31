<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Schemas\main\UpdatesSchema;
use Boctulus\Simplerest\Core\Traits\Uuids;

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

