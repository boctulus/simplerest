<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\MigrationsSchema;

class MigrationsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];
	protected $createdAt = 'created_at';

    function __construct(bool $connect = false){
        parent::__construct($connect, MigrationsSchema::class);
	}	
}

