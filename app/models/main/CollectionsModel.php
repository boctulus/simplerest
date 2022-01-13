<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\main\CollectionsSchema;

class CollectionsModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, CollectionsSchema::class);
	}	
}

