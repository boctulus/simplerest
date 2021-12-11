<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\libs\ValidationRules;
use simplerest\schemas\main\FoldersSchema;

class FoldersModel extends MyModel
{ 
	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, FoldersSchema::class);
	}	
}

