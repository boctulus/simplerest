<?php

namespace simplerest\models\legion;

use simplerest\models\MyModel;
use simplerest\core\Model;
use simplerest\core\libs\ValidationRules;
use simplerest\schemas\legion\TblEpsSchema;

class TblEpsModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblEpsSchema::class);
	}	
}

