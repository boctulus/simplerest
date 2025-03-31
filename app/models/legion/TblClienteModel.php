<?php

namespace Boctulus\Simplerest\Models\legion;

use Boctulus\Simplerest\Models\MyModel;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Schemas\legion\TblClienteSchema;

class TblClienteModel extends MyModel
{ 
	
	

	protected $hidden   = [];
	protected $not_fillable = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, TblClienteSchema::class);
	}	
}

