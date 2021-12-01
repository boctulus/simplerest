<?php

namespace simplerest\models\az;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\models\schemas\az\FooSchema;

class FooModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, new FooSchema());
	}	
}

