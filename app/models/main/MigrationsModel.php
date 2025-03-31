<?php

namespace Boctulus\Simplerest\Models\main;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\main\MigrationsSchema;

class MigrationsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, MigrationsSchema::class);
	}	
}

